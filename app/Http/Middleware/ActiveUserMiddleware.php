<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ActiveUserMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->isActive()) {
            $status = Auth::user()->status;
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $msg = $status === 'pending'
                ? 'Your account is pending administrator approval.'
                : 'Your account has been deactivated. Please contact support.';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ], Response::HTTP_FORBIDDEN);
            }

            return redirect()->route('login')->with('error', $msg);
        }

        return $next($request);
    }
}
