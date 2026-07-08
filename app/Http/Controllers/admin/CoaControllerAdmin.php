<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Base\CoaController;

class CoaControllerAdmin extends CoaController
{
    protected function viewPrefix(): string
    {
        return 'admin';
    }

    protected function routePrefix(): string
    {
        return 'admin';
    }
}
