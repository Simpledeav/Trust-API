<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\MiddlewareNameResolver;

class AuthController extends Controller
{
    public static function middleware(): array
    {
        return [
            'guest:admin' => ['showLoginForm', 'login'],
            'auth:admin' => ['logout', 'dashboard'],
        ];
    }
    
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard')->with('success', 'Logged in successfully');
        }

        return view('admin.auth.login'); 
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard')->with('success', 'Logged in successfully');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        // Invalidate and regenerate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }

    public function dashboard()
    {
        return view('admin.dashboard'); // Create this view
    }
}
