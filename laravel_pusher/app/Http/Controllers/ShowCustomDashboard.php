<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShowCustomDashboard
{
    public function index(Request $request)
    {
        return view('dashboard', [
            'apps' => config('websockets.apps'),
            'path' => config('websockets.path')
        ]);
    }
}
