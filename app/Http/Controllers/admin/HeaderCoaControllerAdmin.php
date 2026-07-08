<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Base\HeaderCoaController;

class HeaderCoaControllerAdmin extends HeaderCoaController
{
    protected function viewPrefix(): string
    {
        return 'admin';
    }
}
