<?php
namespace Bare\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;

use Psr\Log\LoggerInterface;

class ControllerResolver extends BaseControllerResolver
{
    private $container;

    public function __construct(ContainerInterface $container, LoggerInterface $logger = null)
    {
        $this->container = $container;

        parent::__construct($logger ?: new NullLogger);
    }

    /** {@inheritDoc} */
    protected function instantiateController($class)
    {
        $controller = parent::instantiateController($class);

        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }

        return $controller;
    }
}

