<?php

namespace Tests\Feature\Audit;

use Core\Admin\Http\Middleware\RouteRecordMiddleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Tests\Support\IsolatedAuditTestCase;

class RouteRecordingResilienceTest extends IsolatedAuditTestCase
{
    private function request(): Request
    {
        $request = Request::create('/api/orders', 'POST', ['token' => 'private-token']);
        $route = (new Route('POST', 'api/orders', fn () => null))->name('api.orders');
        $route->bind($request);
        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);
        return $request;
    }

    public function test_missing_usage_table_does_not_replace_completed_customer_response(): void
    {
        // The isolated database deliberately has no routes_records table.
        Log::shouldReceive('warning')->once()->with('API usage recording failed', \Mockery::on(fn ($context) =>
            array_keys($context) === ['exception', 'route', 'method'] && $context['route'] === 'api.orders'
        ));
        $response = response()->json(['status' => 'success', 'data' => ['id' => 123]], 201)->header('X-Test', 'preserved');
        $calls = 0;
        $actual = (new RouteRecordMiddleware)->handle($this->request(), function () use ($response, &$calls) {
            $calls++;
            return $response;
        });
        $this->assertSame($response, $actual);
        $this->assertSame(201, $actual->getStatusCode());
        $this->assertSame('preserved', $actual->headers->get('X-Test'));
        $this->assertSame(1, $calls);
    }

    public function test_logging_failure_also_preserves_response(): void
    {
        Log::shouldReceive('warning')->once()->andThrow(new \RuntimeException('log disk unavailable'));
        $response = response()->json(['status' => 'fail'], 422);
        $this->assertSame($response, (new RouteRecordMiddleware)->handle($this->request(), fn () => $response));
    }

    public function test_healthy_recording_still_saves_sanitized_usage_and_preserves_response(): void
    {
        \Illuminate\Support\Facades\Schema::create('routes_records', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            foreach (['version', 'headers', 'method', 'end_point', 'attributes', 'ip_address'] as $column) {
                $table->text($column)->nullable();
            }
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Log::shouldReceive('warning')->never();
        $response = response()->json(['status' => 'success']);
        // Isolate the usage record from the separate global activity-log observer.
        $actual = \Core\Admin\Models\RoutesRecord::withoutEvents(fn () =>
            (new RouteRecordMiddleware)->handle($this->request(), fn () => $response)
        );
        $this->assertSame($response, $actual);
        $records = \Illuminate\Support\Facades\DB::table('routes_records')->get();
        $this->assertCount(1, $records);
        $this->assertSame('api/orders', $records->first()->end_point);
        $this->assertSame('POST', $records->first()->method);
        $this->assertStringNotContainsString('private-token', $records->first()->attributes);
    }

    public function test_business_exception_is_still_propagated(): void
    {
        Log::shouldReceive('warning')->never();
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('business operation failed');
        (new RouteRecordMiddleware)->handle($this->request(), fn () => throw new \RuntimeException('business operation failed'));
    }
}
