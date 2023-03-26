<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();
        ErrorHandler::register(null, false)->setLoggers([
            \E_DEPRECATED => [null],
            \E_USER_DEPRECATED => [null],
        ]);
    }
}
