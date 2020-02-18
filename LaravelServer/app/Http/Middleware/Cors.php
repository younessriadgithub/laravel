<?php

namespace App\Http\Middleware;
 
use Closure;
 
class Cors implements HttpKernelInterface
{
    protected $app;

    public function __construct(HttpKernelInterface $app)
    {
        $this->app = $app;
    }

    public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        // Handle on passed down request
        $response = $this->app->handle($request, $type, $catch);

        $response->headers->set('Access-Control-Allow-Origin' , '*', true);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS, HEAD', true);
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With', true);

        if ($request->getMethod() == 'OPTIONS') {
            $response->setStatusCode(200);
            $response->setContent(null);
        }

        return $response;
    }
}