<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLoginForm()
    {
        // Usa una vista del tema Vuexy para el login
        return view('content.authentications.auth-login-basic');
    }

    /**
     * Maneja la solicitud de inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();

            // Redirige al usuario al dashboard según su rol
            if (Auth::user()->hasRole('Administrador')) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif (Auth::user()->hasRole('Gerente')) { // LÍNEA AÑADIDA
                return redirect()->intended(route('gerente.dashboard'));
            } elseif (Auth::user()->hasRole('Proveedor')) {
                return redirect()->intended(route('proveedor.dashboard'));
            } elseif (Auth::user()->hasRole('Cliente_Corporativo')) {
                return redirect()->intended(route('cliente.dashboard'));
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Muestra el formulario de inicio de sesión para repartidores (guard 'repartidor').
     */
    public function showRiderLoginForm()
    {
        // Puedes usar una vista de login específica para repartidores si lo deseas
        return view('content.authentications.auth-login-basic');
    }

    /**
     * Maneja la solicitud de inicio de sesión para repartidores.
     */
    public function loginRider(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('repartidor')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('repartidor.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }
}
