<?php

namespace Tests\Feature\Audit;

use Core\Orders\Middleware\EnsureClientOwnsOrder;
use Core\Users\Middleware\ActiveUser;
use Core\Users\Middleware\CheckPermissions;
use Core\Users\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Support\IsolatedAuditTestCase;

class AccessProtectionTest extends IsolatedAuditTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->softDeletes();
        });
        DB::table('orders')->insert([
            ['id' => 10, 'client_id' => 1, 'deleted_at' => null],
            ['id' => 20, 'client_id' => 2, 'deleted_at' => null],
            ['id' => 30, 'client_id' => 1, 'deleted_at' => '2026-01-01 00:00:00'],
        ]);
    }

    private function requestFor(int $id, ?User $user): Request
    {
        $request = Request::create('/api/client/orders/'.$id, 'GET');
        $route = new Route('GET', 'api/client/orders/{id}', fn () => null);
        $route->bind($request);
        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    public function test_client_can_access_own_order(): void
    {
        $user = new User(['id' => 1]);
        $user->id = 1;
        $request = $this->requestFor(10, $user);
        $this->assertSame('10', $request->route('id'));
        $this->assertEquals(1, $user->getAuthIdentifier());
        $this->assertTrue(\Core\Orders\Models\Order::whereKey(10)->where('client_id', 1)->exists());
        $response = (new EnsureClientOwnsOrder)->handle($request, fn () => response('allowed'));
        $this->assertSame('allowed', $response->getContent());
    }

    public function test_foreign_missing_deleted_orders_and_guests_are_denied(): void
    {
        $user = new User;
        $user->id = 1;
        foreach ([[20, $user], [999, $user], [30, $user], [10, null]] as [$id, $actor]) {
            $response = (new EnsureClientOwnsOrder)->handle($this->requestFor($id, $actor), function () {
                $this->fail('Unauthorized order request reached controller');
            });
            $this->assertSame(422, $response->getStatusCode());
            $this->assertSame('fail', $response->getData(true)['status']);
            $this->assertArrayHasKey('errors', $response->getData(true));
        }
    }

    public function test_inactive_api_users_are_denied_for_every_accept_header(): void
    {
        foreach ([null, '*/*', 'text/html', 'application/json', 'application/json, text/plain, */*'] as $accept) {
            $request = Request::create('/api/client/orders');
            if ($accept !== null) {
                $request->headers->set('Accept', $accept);
            }
            $request->setUserResolver(fn () => (object) ['is_active' => false]);
            $response = (new ActiveUser)->handle($request, function () {
                $this->fail('Inactive user reached controller');
            });
            $this->assertSame(403, $response->getStatusCode());
            $this->assertFalse($response->getData(true)['status']);
        }
    }

    public function test_active_users_continue_and_inactive_web_users_are_denied(): void
    {
        $request = Request::create('/admin/media-center/list');
        $request->setUserResolver(fn () => (object) ['is_active' => true]);
        $this->assertSame('allowed', (new ActiveUser)->handle($request, fn () => 'allowed'));
        $request->setUserResolver(fn () => (object) ['is_active' => false]);
        $this->expectException(HttpException::class);
        (new ActiveUser)->handle($request, fn () => $this->fail('Inactive web user was allowed'));
    }

    public function test_media_permission_remains_compatible_and_denies_unpermitted_staff(): void
    {
        foreach (['list', 'add-new', 'delete'] as $action) {
            foreach (['dashboard.mediacenter.mymedia', 'dashboard.media-center.'.$action] as $permission) {
                $user = Mockery::mock(User::class)->makePartial();
                $user->shouldReceive('hasRole')->andReturn(false);
                $user->shouldReceive('can')->andReturnUsing(fn ($value) => $value === $permission);
                $request = Request::create('/admin/media-center/'.$action);
                $route = (new Route('GET', 'admin/media-center/'.$action, fn () => null))->name('dashboard.media-center.'.$action);
                $request->setRouteResolver(fn () => $route);
                $request->setUserResolver(fn () => $user);
                $this->assertSame('allowed', (new CheckPermissions)->handle($request, fn () => 'allowed'));
            }
        }
        $deniedUser = Mockery::mock(User::class)->makePartial();
        $deniedUser->shouldReceive('hasRole')->andReturn(false);
        $deniedUser->shouldReceive('can')->andReturn(false);
        $request->setUserResolver(fn () => $deniedUser);
        $this->expectException(HttpException::class);
        (new CheckPermissions)->handle($request, fn () => $this->fail('Unpermitted staff was allowed'));
    }

    public function test_all_sensitive_routes_have_required_middleware(): void
    {
        $found = [];
        foreach ($this->app['router']->getRoutes() as $route) {
            $action = $route->getActionName();
            if (str_contains($action, 'MediaCenterController@')) {
                foreach (['auth', 'active', 'checkPermission'] as $middleware) {
                    $this->assertContains($middleware, $route->gatherMiddleware());
                }
                $found[] = $action;
            }
            if (str_contains($action, 'Api\\Client\\OrdersController@') && in_array(substr(strrchr($action, '@'), 1), ['myOrder', 'updateStatus', 'payFastOrder', 'payFastOrderV2'], true)) {
                $this->assertContains(EnsureClientOwnsOrder::class, $route->gatherMiddleware());
                $found[] = $action;
            }
        }
        $this->assertCount(7, array_unique($found));
    }
}
