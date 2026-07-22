<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdministrationController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $activeUsers = User::where('status', 1)->count();

        return view('modules.administration.index', compact(
            'userCount', 
            'activeUsers'
        ));
    }
}
