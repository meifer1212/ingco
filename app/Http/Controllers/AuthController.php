<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerView()
    {
        return view('register');
    }

    public function loginView()
    {
        return view('login');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name, // $request->name is the name of the input field in the register form
            'email' => $request->email,
            'password' => bcrypt($request->password) // bcrypt() is a helper function that hashes the password
        ]);

        Auth::login($user);

        return redirect()->route('tasks');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('tasks');
        }

        return redirect()->back()->withErrors(['Las credenciales no coinciden']);
    }
}
