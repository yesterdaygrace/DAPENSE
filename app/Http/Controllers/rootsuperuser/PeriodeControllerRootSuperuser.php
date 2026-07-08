<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Http\Controllers\Base\PeriodeController;

class PeriodeControllerRootSuperuser extends PeriodeController
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
