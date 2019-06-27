<?php
/**
 * Created by PhpStorm.
 * User: eduardosalazar
 * Date: 2019-06-15
 * Time: 14:33
 */

namespace App\Services;



use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Lumen\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderBag;

class RequestDelegationService
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * RequestDelegationService constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param HeaderBag $headers
     * @param $service
     * @return \Illuminate\Support\Collection
     */
    public function getHeaderCollection(HeaderBag $headers, $service)
    {
        $headerNames = $headers->keys();
        return collect(array_reduce($headerNames, function ($carry, $headerName) use ($headers) {
            return Arr::add($carry, $headerName, $headers->get($headerName));
        }, []))
            ->except('content-length', 'content-type');
    }

    /**
     * @param Request$request
     * @param $service
     * @return string
     */
    public function getServiceUrl(Request $request, $service)
    {
        return config("gateway.services.{$service}.host") . Str::replaceFirst("/$service", '', $request->getRequestUri());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public function createMultipartBody(Request $request)
    {
        $body = collect();
        foreach ($request->all() as $name => $contents) {
            $data = compact('name', 'contents');
            if ($contents instanceof UploadedFile) {
                $data['contents'] = fopen($contents->getRealPath(), 'r');
                $data['filename'] = $contents->getClientOriginalName();
            }
            $body->add($data);
        }
        return $body;
    }

    /**
     * @param Request $request
     * @param $service
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delegate(Request $request, $service)
    {
        $url = $this->getServiceUrl($request, $service);
        $headers = $this->getHeaderCollection($request->headers, $service)->toArray();
        $options = collect(compact('headers'));

        if (!$request->files->count()) {
            $options->put('form_params', $request->request->all());
        } else {
            $options->put('multipart', $this->createMultipartBody($request));
        }
        try {
            $response = $this->client->request($request->method(), $url, $options->toArray());
            return response($response->getBody(), $response->getStatusCode(), $response->getHeaders());
        } catch (\Throwable $e) {
            $serviceName = ucfirst($service);

            $response = [
                'message' => "Something went wrong when trying connect with {$serviceName} service",
            ];

            if (config('app.debug')) {
                $response['trace'] = $e->getTraceAsString();
            }

            return response()->json($response, 500);
        }
    }
}
