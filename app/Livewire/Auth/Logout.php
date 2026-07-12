<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Logout extends Component
{
    public $tampilan;
    public function mount($tampilan = 'logout1')
    {
        $this->tampilan = $tampilan;
    }

    public function render()
    {
        if ($this->tampilan == 'logout1') {
            return view('livewire.auth.logout1');
        } else {
            return view('livewire.auth.logout2');
        }
    }

    public function _logout()
    {
        if (!Auth::check()) {
            flash('danger', 'Session telah berakhir, silahkan anda login!');
            return $this->redirect(route('frontend.site'));
        }

        session()->flush();
        Auth::logout();
        Cache::flush();

        return $this->redirect(route('frontend.site'));
    }
}
