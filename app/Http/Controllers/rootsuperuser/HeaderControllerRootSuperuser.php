<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Http\Controllers\Base\HeaderController;

class HeaderControllerRootSuperuser extends HeaderController
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
