<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Login via AJAX (modal di halaman depan). Mengembalikan JSON:
     *  - sukses  : { status: true, redirect: <url dashboard> }
     *  - gagal   : { status: false, message: <pesan> }
     * Logika (rate limiting, anti-enumeration, session role) dibuat sama
     * dengan komponen Livewire Auth\Login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'tahun'    => 'required',
        ]);

        $throttleKey = Str::lower($request->username) . '|' . $request->ip();
        $maxAttempts = 5;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status'  => false,
                'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.',
            ], 429);
        }

        $user = User::where('username', $request->username)->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            $sisa = RateLimiter::remaining($throttleKey, $maxAttempts);

            $pesan = 'Username atau password salah, silakan periksa kembali!';
            if ($sisa <= 0) {
                $detik = RateLimiter::availableIn($throttleKey);
                $pesan = 'Terlalu banyak percobaan login. Akun dikunci sementara, coba lagi dalam ' . $detik . ' detik.';
            } else {
                $pesan .= ' Sisa ' . $sisa . ' percobaan sebelum akun dikunci sementara.';
            }

            return response()->json(['status' => false, 'message' => $pesan], 401);
        }

        if (!$user->is_active) {
            RateLimiter::hit($throttleKey, 60);
            return response()->json(['status' => false, 'message' => 'Akun Anda sedang dinonaktifkan.'], 403);
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user);

        $roleCount = $user->roles()->count();
        if ($roleCount == 1) {
            $role = $user->roles()->first()->name;
            session()->put(['role' => $role, 'tahun' => $request->tahun]);
            $redirect = in_array($role, ['keuangan', 'kepegawaian'])
                ? route($role . '.dashboard')
                : route('admin.dashboard');
        } else {
            session()->put(['role' => null, 'tahun' => $request->tahun]);
            $redirect = route('admin.dashboard');
        }

        return response()->json(['status' => true, 'redirect' => $redirect]);
    }
}
