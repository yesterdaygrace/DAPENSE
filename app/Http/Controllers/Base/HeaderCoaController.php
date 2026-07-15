<?php

namespace App\Http\Controllers\Base;

use App\Models\HeaderCOA;
use Illuminate\Support\Facades\Auth;

class HeaderCoaController
{
    protected function viewPrefix(): string
    {
        return Auth::user()->usertype;
    }

    public function index()
    {
        $headerCoas = HeaderCOA::paginate(10);

        return view($this->viewPrefix() . '.account.headercoa.home', compact('headerCoas'));
    }
}
