<?php

namespace Pachuli\Web\Http;

class Kernel
{
    protected Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function resolve(): void
    {
        $this->router->dispatcher();
    }
}