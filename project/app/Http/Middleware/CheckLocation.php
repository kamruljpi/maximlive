<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\IpCheckCOntroller;
class CheckLocation
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
        $code = strtolower(IpCheckCOntroller::checkIp());

        $bd = $request->myAttribute = array(strtolower('BD'));

        if (!(in_array($code, $bd ) ) ) {
            
            return redirect()->route('restricted');
        }

        return $next($request);
    }
}
