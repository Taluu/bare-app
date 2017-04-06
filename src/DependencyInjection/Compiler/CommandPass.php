<?php

namespace Bare\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class CommandPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('console.command.ids', array_keys(
            $container->findTaggedServiceIds('console.command')
        ));
    }
}
