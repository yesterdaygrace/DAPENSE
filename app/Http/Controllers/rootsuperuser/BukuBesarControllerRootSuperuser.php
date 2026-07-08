<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Http\Controllers\Base\BukuBesarController;

class BukuBesarControllerRootSuperuser extends BukuBesarController
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
