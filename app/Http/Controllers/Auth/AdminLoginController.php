<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Step 1: Verifikasi credentials.
     * - Admin biasa  → langsung login ke /dashboard
     * - Super admin  → simpan pending session, redirect ke halaman captcha
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password'  => 'required|string',
        ]);

        $loginField = filter_var($request->input('login_id'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->input('login_id'),
            'password'  => $request->input('password'),
        ];

        // Coba autentikasi dengan Auth::attempt (support password hash maupun plain)
        if (!Auth::guard('admin')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'login_id' => trans('auth.failed'),
            ]);
        }

        $admin = Auth::guard('admin')->user();

        if ($admin->role === 'super_admin') {
            // Belum sepenuhnya login — logout dulu, lalu simpan pending di session
            Auth::guard('admin')->logout();

            $request->session()->put('pending_super_admin_id', $admin->id_admin);

            return redirect()->route('login.captcha');
        }

        // Admin biasa — sudah login via attempt(), regenerate session saja
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    /**
     * Step 2 (super_admin): tampilkan form captcha.
     */
    public function showCaptchaForm()
    {
        if (!session()->has('pending_super_admin_id')) {
            return redirect()->route('login');
        }
        return view('auth.captcha');
    }

    /**
     * Step 2 (super_admin): verifikasi captcha lalu login penuh.
     */
    public function verifyCaptcha(Request $request)
    {
        if (!session()->has('pending_super_admin_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'captcha' => 'required|captcha',
        ], [
            'captcha.captcha' => 'Kode CAPTCHA yang dimasukkan salah.',
        ]);

        $adminId = $request->session()->get('pending_super_admin_id');

        $admin = Admin::find($adminId);

        if (!$admin) {
            $request->session()->forget(['pending_super_admin_id', 'pending_remember']);
            return redirect()->route('login')
                ->withErrors(['login_id' => 'Sesi tidak valid, silakan login ulang.']);
        }

        Auth::guard('admin')->login($admin);
        $request->session()->forget(['pending_super_admin_id', 'pending_remember']);
        $request->session()->regenerate();

        return redirect()->intended('/admins');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function refreshCaptcha()
    {
        return response()->json(['captcha' => captcha_img('flat')]);
    }
}
