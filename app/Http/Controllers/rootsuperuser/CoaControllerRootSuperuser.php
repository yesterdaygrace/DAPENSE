<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Http\Controllers\Base\CoaController;

class CoaControllerRootSuperuser extends CoaController
{
    protected function viewPrefix(): string
    {
        return 'rootsuperuser';
    }

    protected function routePrefix(): string
    {
        return 'rootsuperuser';
    }
}
