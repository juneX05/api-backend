<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:8081',
            'http://192.168.43.176:8081',
            'https://hy-sys.herokuapp.com',
            'https://wilz8.csb.app',
            'https://15yvk.csb.app',
        ];
        $requestOrigin = $request->headers->get('origin');
    
        if (in_array($requestOrigin, $allowedOrigins)) {
            return $next($request)
                ->header('Access-Control-Allow-Origin', $requestOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }
    
        return $next($request);
    }
}
