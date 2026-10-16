<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{
    public function index()
    {
        if (Auth::guest()) {
            return redirect()->route('login');
        }
        return redirect()->route('dashboard');
    }

    public function dashboard()
    {
        return view('app.dashboard');
    }
}
