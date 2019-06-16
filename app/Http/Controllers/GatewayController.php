<?php
/**
 * Created by PhpStorm.
 * User: eduardosalazar
 * Date: 2019-06-09
 * Time: 17:01
 */

namespace App\Http\Controllers;


use App\Services\RequestDelegationService;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function dispatchRequest(Request $request, RequestDelegationService $delegationService, $service)
    {
        return $delegationService->delegate($request, $service);
    }
}
