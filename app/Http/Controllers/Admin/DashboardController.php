<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('content.admin.dashboard');
    }

    public function reports()
    {
        // Vista simple de Reports para Administrador
        return view('content.admin.reports');
    }
}
