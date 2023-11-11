<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'name' => 'required|min:3|max:255'
        ], [], [
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'name' => 'nombre'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password) // bcrypt() is a helper function that hashes the password
        ]);

        Auth::login($user);

        return redirect()->route('tasks.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [], [
            'email' => 'correo electrónico',
            'password' => 'contraseña'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('tasks.index');
        }

        return redirect()->back()->withInput()->withErrors(
            'Las credenciales no coinciden con nuestros registros'
        );
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
