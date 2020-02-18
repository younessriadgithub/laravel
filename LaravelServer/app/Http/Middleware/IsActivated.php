<?php

namespace App\Http\Middleware;
use Tymon\JWTAuth\Facades\JWTAuth;

use Closure;

class IsActivated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $th = JWTAuth::authenticate($request->bearerToken());
        $array = json_decode($th, true);
        
        if ($array['email_verified_at'] == null) {
            return response()->json(['inactive'=>true ]);

        }

        return $next($request);
    }
}
