<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Base\OtorisatorController;

class OtorisatorControllerAdmin extends OtorisatorController
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
