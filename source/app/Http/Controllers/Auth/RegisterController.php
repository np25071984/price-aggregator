<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
        ], [
            'photos.required' => 'Имя пользователя обязательно для заполнения',
            'email.required' => 'Адрес электронной почты обязателен для заполнения',
            'email.email' => 'Введенный адрес элктронной почты не валиден',
            'email.unique' => 'Адрес электронной почты уже занят. Введите новый или воспользуйтесь функцией восстановления пароля',
            'password.required' => 'Пароль обязателен для заполнения',
            'password.confirmed' => 'Пароль и подтверждение пароля не совпадают',
            'password.min' => 'Пароль не должен быть меньше 8 символов',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('get-aggregation');
    }
}
