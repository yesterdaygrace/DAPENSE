<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Base\SaldoAwalController;

class SaldoAwalControllerAdmin extends SaldoAwalController
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
