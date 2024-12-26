<?php

namespace App\Responses;

use App\Providers\RouteServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // ここで確実にリダイレクト先を制御できます
        if ($request->is(config('fortify.routes.admin'))) {
            return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
        }
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}