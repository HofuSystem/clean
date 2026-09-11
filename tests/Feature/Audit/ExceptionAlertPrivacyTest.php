<?php

namespace Tests\Feature\Audit;

use Core\Notification\Services\TelegramNotificationService;
use Core\Notification\Jobs\SendTelegramMessageJob;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\Support\IsolatedAuditTestCase;

class ExceptionAlertPrivacyTest extends IsolatedAuditTestCase
{
    public function test_exception_alert_excludes_request_and_sql_secrets(): void
    {
        $request = Request::create('/api/payments/private-path-token?token=query-secret', 'POST', [
            'otp' => 'otp-secret', 'nested' => ['password' => 'body-secret'],
        ]);
        $request->headers->set('Authorization', 'Bearer header-secret');
        $request->headers->set('Cookie', 'session=cookie-secret');
        $request->setUserResolver(fn () => (object) ['email' => 'private@example.test', 'id' => 42]);
        $route = new Route('POST', 'api/payments/{payment}', fn () => null);
        $route->bind($request);
        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);
        $exception = new QueryException('testing', 'select * from users where token = ?', ['binding-secret'], new \RuntimeException('exception-secret'));
        $message = app(TelegramNotificationService::class)->formatExceptionMessage($exception);
        foreach (['private-path-token', 'query-secret', 'otp-secret', 'body-secret', 'header-secret', 'cookie-secret', 'private@example.test', 'binding-secret', 'exception-secret', 'select *'] as $secret) {
            $this->assertStringNotContainsString($secret, $message);
        }
        $this->assertStringContainsString('api/payments/{payment}', $message);
        $this->assertStringContainsString('QueryException', $message);
        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    public function test_html_is_escaped_and_unmatched_paths_are_not_included(): void
    {
        config(['app.name' => '<b>untrusted & name</b>']);
        $this->app->instance('request', Request::create('/secret-unmatched-path'));
        $message = app(TelegramNotificationService::class)->formatExceptionMessage(new \RuntimeException('private'));
        $this->assertStringContainsString('&lt;b&gt;untrusted &amp; name&lt;/b&gt;', $message);
        $this->assertStringContainsString('(unmatched)', $message);
        $this->assertStringNotContainsString('secret-unmatched-path', $message);
    }

    public function test_formatter_works_without_request_binding(): void
    {
        $this->app->offsetUnset('request');
        $message = app(TelegramNotificationService::class)->formatExceptionMessage(new \RuntimeException('private'));
        $this->assertStringContainsString('RuntimeException', $message);
        $this->assertStringNotContainsString('private', $message);
    }

    public function test_telegram_failed_response_is_not_logged_verbatim(): void
    {
        Http::swap(new \Illuminate\Http\Client\Factory);
        Http::preventStrayRequests();
        Http::fake(['*' => Http::response('private-response-body', 400)]);
        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once()->with('Telegram API request failed', ['status' => 400]);
        (new SendTelegramMessageJob('test-chat', 'test-message'))->handle();
    }

    public function test_telegram_connection_failure_does_not_log_request_url_or_token(): void
    {
        Http::swap(new \Illuminate\Http\Client\Factory);
        Http::preventStrayRequests();
        Http::fake(fn () => throw new \RuntimeException('https://example.test/bot/private-token'));
        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once()->with('SendTelegramMessageJob failed', ['exception' => \RuntimeException::class]);
        (new SendTelegramMessageJob('test-chat', 'test-message'))->handle();
    }
}
