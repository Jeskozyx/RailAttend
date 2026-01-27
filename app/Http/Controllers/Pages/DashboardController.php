<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Train;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index() 
    {
        $roles = Auth::user()->getRoleNames()[0];
        
        if ($roles === "Kondektur") {
            $train = Train::orderBy('id', 'DESC')->get();
            return view("pages.dashboard", compact("train"));
        } else {
            # code...
            return view("pages.dashboard");
        }
    }
}
