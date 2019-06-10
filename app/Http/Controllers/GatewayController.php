<?php
/**
 * Created by PhpStorm.
 * User: eduardosalazar
 * Date: 2019-06-09
 * Time: 17:01
 */

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function dispatchRequest(Request $request, Client $client, $service)
    {
        $host = config("gateway.services.{$service}.host");
        $requestUri = $request->getRequestUri();

        $response = $client->request($request->method(),"{$host}{$requestUri}");
        return response($response->getBody(), $response->getStatusCode(), $response->getHeaders());
    }
}
