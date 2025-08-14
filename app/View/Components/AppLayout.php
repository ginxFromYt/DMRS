<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\View\Component;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->roles->contains('name', 'SuperAdmin')) {
                return view('layouts.SuperAdminLO.app');
            } elseif ($user->roles->contains('name', 'Client')) {
                return view('layouts.Client_LO.app');
            }
            elseif ($user->roles->contains('name', 'Admin')) {
                return view('layouts.Admin_LO.app');
            }
        }
        // $user = Auth::user();
        // if ($user->roles->first() && $user->roles->first()->name === 'SuperAdmin') {
        //     return view('layouts.admin');
        // } elseif ($user->roles->where('name', 'user')->isNotEmpty()) {
        //     return view('layouts.user');
        // }
        return view('layouts.app');
    }
}
