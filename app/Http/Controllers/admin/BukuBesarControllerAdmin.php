<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Base\BukuBesarController;

class BukuBesarControllerAdmin extends BukuBesarController
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
