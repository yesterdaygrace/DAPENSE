<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Base\PeriodeController;

class PeriodeControllerAdmin extends PeriodeController
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
