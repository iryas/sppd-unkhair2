<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChangeRoleController extends Controller
{
    //
    public function index($role)
    {
        // dd($role);

        request()->session()->put([
            'role' => $role
        ]);

        alert()->success('Success', 'Sukses ganti peran, Selamat datang ' . auth()->user()->name);

        if (in_array($role, ['keuangan', 'kepegawaian'])) {
            return redirect(route($role . '.dashboard'));
        }

        return redirect(route('admin.dashboard'));
    }
}
