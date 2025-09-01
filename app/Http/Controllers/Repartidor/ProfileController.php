<?php

namespace App\Http\Controllers\Repartidor;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show()
    {
        return view('content.repartidor.profile');
    }
}

