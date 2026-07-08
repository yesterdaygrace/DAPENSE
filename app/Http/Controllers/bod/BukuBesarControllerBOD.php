<?php

namespace App\Http\Controllers\bod;

use App\Http\Controllers\Base\BukuBesarController;

class BukuBesarControllerBOD extends BukuBesarController
{
    protected function viewPrefix(): string
    {
        return 'bod';
    }

    protected function routePrefix(): string
    {
        return 'bod';
    }
}
