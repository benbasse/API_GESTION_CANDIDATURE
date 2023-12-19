<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */


    // public function handle(Request $request, Closure $next, string $role): Response
    // {
    //     if (auth()->check() && auth()->user()->role === $role) {

    //         return $next($request);
    //     } else {
    //         return redirect('/')->with('status', 'Vous netes pas autoriser a acceder a cette page');
    //     }
    // }

    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        return response()->json(['error' => 'Vous n\'etes pas autoriser a acceder a cette page '], 403);
    }
}
