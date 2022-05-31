<?php

namespace App\Http\Middleware;

use App\Enum\UserStatusEnum;
use Closure;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class AuthUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $session_token = session('session_token');

        if (!$session_token && $request->is('dashboard/*')) {
            return redirect()->route('login');
        }

        if (!$session_token) {
            return $next($request);
        }

        $request->user = User::whereSessionToken($session_token)->first();

        if ((!$request->user || $request->user->status === UserStatusEnum::SUSPENDED) && $request->is('dashboard/*')) {
            if ($request->user) {
                $request->user->invalidateSession();
            }

            session()->forget('session_token');

            return redirect()->route('login');
        }

        try {
            $request->user->makeVisible('session_token');

            \Firebase\JWT\JWT::decode($session_token, new \Firebase\JWT\Key(config('app.key'), 'HS256'));

            $request->user->updateSession();

            if (!$request->is('dashboard/*')) {
                return redirect()->route('dashboard.users.index');
            }

            return $next($request);
        } catch (Exception $e) {
            if ($request->user) {
                $request->user->invalidateSession();
            }

            session()->forget('session_token');

            return redirect()->route('login');
        }
    }
}
