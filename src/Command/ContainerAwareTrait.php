<?php
namespace Bare\Command;

use Symfony\Component\DependencyInjection\ContainerAwareTrait as BaseTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

trait ContainerAwareTrait
{
    use BaseTrait;

    /**
     * @return ContainerInterface
     *
     * @throws \LogicException
     */
    protected function getContainer()
    {
        if (null === $this->container) {
            $application = $this->getApplication();

            if (null === $application) {
                throw new \LogicException('The container cannot be retrieved as the application instance is not yet set.');
            }

            $this->container = $application->getKernel()->getContainer();
        }

        return $this->container;
    }
}

