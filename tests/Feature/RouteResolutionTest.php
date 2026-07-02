<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;

class RouteResolutionTest extends TestCase
{
    public function test_static_create_routes_are_not_resolved_as_model_ids(): void
    {
        $routes = app('router')->getRoutes();

        foreach ([
            '/customers/create' => 'customers.create',
            '/cases/create' => 'cases.create',
            '/nasabah/tambah' => 'legacy.customers.create',
            '/berkas/tambah' => 'legacy.cases.create',
        ] as $path => $expectedRouteName) {
            $this->assertSame(
                $expectedRouteName,
                $routes->match(Request::create($path, 'GET'))->getName(),
                "Route {$path} must resolve to {$expectedRouteName}."
            );
        }
    }
}
