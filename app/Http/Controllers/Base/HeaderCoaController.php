<?php

namespace App\Http\Controllers\Base;

use App\Models\HeaderCOA;

class HeaderCoaController
{
    protected function viewPrefix(): string
    {
        return \Illuminate\Support\Facades\Auth::user()->usertype;
    }

    public function index()
    {
        $headerCoas = HeaderCOA::paginate(10);

        return view($this->viewPrefix().'.account.headercoa.home', compact('headerCoas'));
    }
}
