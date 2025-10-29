<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class WebAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('auth_token') ?? $request->bearerToken();
        
        if (!$token) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect('/login');
        }

        try {
            $request->headers->set('Authorization', 'Bearer ' . $token);
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                return redirect('/login');
            }
            
            auth()->setUser($user);
            
        } catch (JWTException $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect('/login');
        }

        return $next($request);
    }
}
