<?php
namespace Bare;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

use Bare\DependencyInjection\BareExtension;

class BareApp extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterListenersPass, PassConfig::TYPE_BEFORE_REMOVING);
    }

    /** {@inheritDoc} */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new BareExtension;
        }

        return $this->extension;
    }
}

