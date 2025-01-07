<?php

namespace App\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        if ($request->is('admin/*')) {
            return redirect('/admin/login');
        }
        return redirect('/login');
    }
}