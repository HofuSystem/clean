<?php

namespace Tests\Feature\Audit;

use Illuminate\Http\Request;
use Tests\Support\IsolatedAuditTestCase;

class WalletTransactionRoutesTest extends IsolatedAuditTestCase
{
    public function test_wallet_detail_routes_only_match_numeric_ids(): void
    {
        $routes = app('router')->getRoutes();
        foreach (['show' => 'GET', 'edit' => 'PUT', 'delete' => 'DELETE', 'comment' => 'POST', 'restore' => 'PUT'] as $action => $method) {
            $route = $routes->getByName('dashboard.wallet-transactions.'.$action);
            $this->assertNotNull($route);
            $url = route($route->getName(), ['id' => 123]);
            $this->assertTrue($route->matches(Request::create($url, $method)), $action);
            foreach (['undefined', 'null', '123abc', '1.5', '-1'] as $invalid) {
                $this->assertFalse($route->matches(Request::create(str_replace('/123', '/'.$invalid, $url), $method)), $action.': '.$invalid);
            }
        }
    }

    public function test_static_wallet_routes_keep_their_handlers(): void
    {
        $routes = app('router')->getRoutes();
        foreach (['stats', 'search-users', 'create', 'import', 'export'] as $action) {
            $name = 'dashboard.wallet-transactions.'.$action;
            $matched = $routes->match(Request::create(route($name), 'GET'));
            $this->assertSame($name, $matched->getName());
        }
    }
}
