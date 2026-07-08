<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Http\Controllers\Base\OtorisatorController;

class OtorisatorControllerRootSuperuser extends OtorisatorController
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
