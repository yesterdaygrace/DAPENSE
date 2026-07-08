<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Http\Controllers\Base\HeaderCoaController;

class HeaderCoaControllerRootSuperuser extends HeaderCoaController
{
    protected function viewPrefix(): string
    {
        return 'rootsuperuser';
    }
}
