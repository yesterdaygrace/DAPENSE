<?php

namespace App\Http\Controllers\bod;

use App\Http\Controllers\Base\JurnalingController;

class JurnalingControllerBOD extends JurnalingController
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
