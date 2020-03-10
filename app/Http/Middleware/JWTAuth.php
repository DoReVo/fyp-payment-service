<?php

namespace App\Http\Middleware;

use App\Helpers\JWTHelper;
use Closure;

// use Illuminate\Http\Request;

class JWTAuth
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

        // New JWTHelper instance
        $jwtHandler = new JWTHelper;

        // Get JWT Token from Authorization header
        $token = $request->bearerToken();

        try {
            // if token does not exist
            if (!$token) {
                throw new \Exception("No access token provided");
            }
            // tries to decode token if it exist,
            // will throw error if failed
            $decodedToken = $jwtHandler->decode($token);
        } catch (\Throwable $th) {
            return response(array('error' => $th->getMessage()), 403);
            // return $request->bearerToken();
        }

        return $next($request);

    }
}
