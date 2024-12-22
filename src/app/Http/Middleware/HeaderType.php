<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HeaderType
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
        $year = date('Y');
        $month = date('m');
        $headerType = $this->determineHeaderType($request);
        $request->merge([
            'year' => $year,
            'month' => $month,
            'headerType' => $headerType
        ]);
        return $next($request);
    }

    public function determineHeaderType(Request $request): string
    {
        /*
        * ロゴのみの/login, /registerの場合はnullが渡される
        */
        if (auth()->check()) {
            // 認証済の場合、ログアウトボタンを表示
            return 'logOut';
        } else {
            // 未認証の場合、ログインボタンを表示
            return 'logIn';
        }
    }
}
