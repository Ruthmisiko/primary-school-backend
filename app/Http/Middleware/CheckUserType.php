<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$userTypes): Response
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = Auth::user();
        
        // Check if user's type is in the allowed types
        if (!in_array($user->userType, $userTypes)) {
            return response()->json([
                'message' => 'Access denied. Insufficient permissions.',
                'required_types' => $userTypes,
                'user_type' => $user->userType
            ], 403);
        }

        return $next($request);
    }
} 