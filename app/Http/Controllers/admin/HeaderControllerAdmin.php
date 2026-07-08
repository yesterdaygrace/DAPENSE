<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Base\HeaderController;

class HeaderControllerAdmin extends HeaderController
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
