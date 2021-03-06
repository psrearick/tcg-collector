<?php

namespace App\Http\Middleware;

use App\Domain\Collections\Models\Collection;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyCollectionIsPublic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        $authenticated = false;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $authenticated = true;
            }
        }

        $uuid         = $request->route('collection');
        $collection   = Collection::uuid($uuid);
        // if (!$collection) {
        // return redirect(RouteServiceProvider::HOME);
        // }

        // if ($collection->is_public) {
        // return $next($request);
        // }

        if (!$authenticated) {
            return redirect(RouteServiceProvider::HOME);
        }

        // $collection = Collection::find($id);
        if (!$collection) {
            abort(404);
        }

        return $next($request);
    }
}
