<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.require' => 'Адрес электронной почти обязателен для заполнения',
            'email.email' => 'Неверный формат адреса электронной почты',
        ]);

       $status = Password::sendResetLink(
           $request->only('email')
       );

       return $status === Password::RESET_LINK_SENT
           ? back()->with('status', __($status))
           : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
