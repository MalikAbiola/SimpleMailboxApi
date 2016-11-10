<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;

class AuthorizationMiddlware
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
        $authorizationHeader = $request->header('Authorization', '');

        if (strlen($authorizationHeader) < 8) {
            return app(Controller::class)->errorUnauthorized("Please provide an appropriate Authorization header.");
        }

        return $next($request);
    }
}
