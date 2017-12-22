<?php
namespace TechWilk\Church\Teachings\Controller;

class AbstractController
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
}
