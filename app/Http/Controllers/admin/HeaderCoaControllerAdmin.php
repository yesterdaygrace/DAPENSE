<?php

namespace App\Http\Controllers\admin;

use App\Models\HeaderCOA;
use Illuminate\Http\Request;

class HeaderCoaControllerAdmin
{
    public function index()
    {
        $headerCoas = HeaderCOA::paginate(10);
        return view('admin.account.headercoa.home', compact('headerCoas'));
    }
}
