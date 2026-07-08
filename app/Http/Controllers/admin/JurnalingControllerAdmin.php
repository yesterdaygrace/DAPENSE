<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Base\JurnalingController;

class JurnalingControllerAdmin extends JurnalingController
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
