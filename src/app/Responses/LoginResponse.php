<?php

namespace App\Responses;

use App\Providers\RouteServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    // FortifyServiceProvider.phpでバインドしている
    public function toResponse($request)
    {
        if ($request->is('admin/*')) {
            return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
        }
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}