<?php
namespace App\Http\Controllers\Cliente;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('content.cliente.dashboard');
    }
}
