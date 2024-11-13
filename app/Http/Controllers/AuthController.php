<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $user = DB::table('users')->where('email', $request->email)->first();
        
        if (!$user) {
        return back()->with('error', 'Email không tồn tại');
    	}

        if (Hash::check($request->password, $user->password)) {
        Session::put('user_id', $user->ID);
        Session::put('user_name', $user->name);
        return redirect('/brac');
    	}

        return back()->with('error', 'Invalid email or password');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Đăng ký thành công! Hãy đăng nhập.');
    }

    public function logout()
    {
        Session::flush();
        return redirect('/login');
    }
}