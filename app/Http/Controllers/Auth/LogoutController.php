<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Cierra la sesión de los usuarios del guard 'web'.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Cierra la sesión de los usuarios del guard 'repartidor'.
     */
    public function logoutRider(Request $request)
    {
        Auth::guard('repartidor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/repartidor/login');
    }
}
