<?php
/**
 * Created by PhpStorm.
 * User: eduardosalazar
 * Date: 2019-06-09
 * Time: 15:41
 */

namespace App\Services;


use App\Http\Controllers\GatewayController;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Router;

class RouteRegistrationService
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * RouteRegistrationService constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param Request $request
     */
    public function register(Request $request)
    {
        $method = strtolower($request->method());
        $serviceName = Arr::first(explode('/', $request->path()));
        if($this->serviceExists($serviceName)) {
            $route = Str::replaceFirst($serviceName, '{service}', $request->path());
            $this->createRoute($method, $route, $serviceName);
        }
    }

    /**
     * @param string $service
     * @return bool
     */
    protected function serviceExists($service)
    {
        return !!config("gateway.services.{$service}");
    }

    /**
     * @param $method
     * @param $route
     * @param $service
     */
    protected function createRoute($method, $route, $service)
    {
        $this->router->group(['middleware' => config("gateway.services.{$service}.middleware")], function () use ($method, $route) {
            $this->router->{$method}("{$route}", GatewayController::class . "@dispatchRequest");
        });
    }
}
