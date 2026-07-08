<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Http\Controllers\Base\JurnalingController;

class JurnalingControllerRootSuperuser extends JurnalingController
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
