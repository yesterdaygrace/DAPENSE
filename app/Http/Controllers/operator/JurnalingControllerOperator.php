<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Base\JurnalingController;

class JurnalingControllerOperator extends JurnalingController
{
    protected function viewPrefix(): string
    {
        return 'operator';
    }

    protected function routePrefix(): string
    {
        return 'operator';
    }
}
