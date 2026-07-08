<?php

namespace App\Http\Controllers\Base;

use App\Models\HeaderCOA;

abstract class HeaderCoaController
{
    abstract protected function viewPrefix(): string;

    public function index()
    {
        $headerCoas = HeaderCOA::paginate(10);

        return view($this->viewPrefix().'.account.headercoa.home', compact('headerCoas'));
    }
}
