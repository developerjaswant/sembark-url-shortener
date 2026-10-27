<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->role, $roles, true)) {
            if ($user->role === 'SuperAdmin') {
                $target = route('superadmin.dashboard');
            } elseif ($user->role === 'Admin') {
                $target = route('admin.dashboard');
            } elseif ($user->role === 'Member') {
                $target = route('member.dashboard');
            } else {
                $target = url('/');
            }

            return redirect($target)->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
