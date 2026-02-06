<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Email Harus Di Isi',
            'email.exists' => 'Email Tidak Terdaftar Di Sistem',
            'password.required' => 'Password Harus Di Isi',
            'password.min' => 'Password Minimal 8 Angka atau Huruf',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();
                
                // Mark user as online
                $user->is_online = true;
                $user->save();
                
                return redirect()->route('dashboard');
            } else {
                return back()->with('status', 'Email/Password Salah.');
            }
        }

        return back()->with('status', 'Email/Password Salah.');
    }

    public function logout(Request $request) 
    {
        // Mark user as offline before logout
        $user = Auth::user();
        if ($user) {
            $user->is_online = false;
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
