<?php

namespace App\Http\Controllers\operator;

use App\Models\HeaderCOA;
use Illuminate\Http\Request;

class HeaderCoaControllerOperator
{
    public function index()
    {
        $headerCoas = HeaderCoa::paginate(10);
        return view('operator.account.headercoa.home', compact('headerCoas'));
    }
}
