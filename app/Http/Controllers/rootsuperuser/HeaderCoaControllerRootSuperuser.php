<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Models\HeaderCOA;
use Illuminate\Http\Request;

class HeaderCoaControllerRootSuperuser
{
    public function index()
    {
        $headerCoas = HeaderCOA::paginate(10);
        return view('rootsuperuser.account.headercoa.home', compact('headerCoas'));
    }
}
