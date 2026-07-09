<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Login extends Component
{
    public $username;
    public $password;
    public $tahun;
    public $bool_peserta = 0;

    public function render()
    {
        $data = [
            'judul' => 'Login Aplikasi'
        ];

        return view('livewire.auth.login2', $data)
            ->extends('layouts.auth2')
            ->section('content');
    }

    public function checklogin()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
            'tahun' => 'required'
        ]);

        // Rate limiting: cegah brute-force (maks 5 percobaan gagal / menit per username+IP)
        $throttleKey = Str::lower($this->username) . '|' . request()->ip();
        $maxAttempts = 5;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            flash('error', 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.');
            return $this->redirect(route('auth.login'));
        }

        $login = User::where('username', $this->username)->first();

        // Pesan generik (tidak membedakan username vs password) untuk mencegah user enumeration
        if (!$login || !password_verify($this->password, $login->password)) {
            RateLimiter::hit($throttleKey, 60);
            $sisa = RateLimiter::remaining($throttleKey, $maxAttempts);

            $pesan = 'Username atau password salah, silakan periksa kembali!';
            if ($sisa <= 0) {
                $seconds = RateLimiter::availableIn($throttleKey);
                $pesan = 'Terlalu banyak percobaan login. Akun dikunci sementara, coba lagi dalam ' . $seconds . ' detik.';
            } else {
                $pesan .= ' Sisa ' . $sisa . ' percobaan sebelum akun dikunci sementara.';
            }

            flash('error', $pesan);
            return $this->redirect(route('auth.login'));
        }

        if (!$login->is_active) {
            RateLimiter::hit($throttleKey, 60);
            flash('error', 'Akun anda sedang dinonaktifkan!!');
            return $this->redirect(route('auth.login'));
        }

        RateLimiter::clear($throttleKey);

        {
            Auth::login($login);
            $role = $login->roles()->count();

            if ($role == 1) {
                $role = '';
                foreach ($login->roles()->get() as $r) {
                    $role = $r->name;
                }
                session()->put([
                    'role' => $role,
                    'tahun' => $this->tahun
                ]);

                alert()->success('Success', 'Berhasil Login, Selamat datang ' . $login->name);

                if (in_array($role, ['keuangan', 'kepegawaian'])) {
                    return $this->redirect(route($role . '.dashboard'));
                }

                return $this->redirect(route('admin.dashboard'));
            } else {
                session()->put([
                    'role' => NULL,
                    'tahun' => $this->tahun
                ]);
                alert()->success('Success', 'Berhasil Login, Selamat datang ' . $login->name);
                return $this->redirect(route('admin.dashboard'));
            }
        }
    }
}
