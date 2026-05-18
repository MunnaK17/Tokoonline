<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginBackend()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('backend.v_login.login', [
            'judul' => 'Login',
        ]);
    }

    public function authenticateBackend(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->status == 0) {
                Auth::logout();
                return back()->with('error', 'User belum aktif');
            }

            $request->session()->regenerate();
            return $this->redirectByRole();
        }

        return back()->with('error', 'Login Gagal');
    }

    public function logoutBackend()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect(route('backend.login'));
    }

    private function redirectByRole()
    {
        if (in_array((int) Auth::user()->role, [0, 1], true)) {
            return redirect()->route('backend.beranda');
        }

        return redirect()->route('beranda')->with('success', 'Anda berhasil login.');
    }
}
