<?php
namespace Bare\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use Bare\Amqp;

class BareExtension extends Extension
{
    /** {@inheritDoc} */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration;
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('amqp.xml');
        $loader->load('framework/services.xml');
        $loader->load('framework/routing.xml');

        $this->loadAmqpConfiguration($container, $config['amqp']);
    }

    private function loadAmqpConfiguration(ContainerBuilder $container, array $config)
    {
        $connection = $config['connection'];
        $broker = new Definition(Amqp\Broker::class, [$connection['host'], $connection['port'], $connection['login'], $connection['password'], $connection['virtual_host']]);
        $container->setDefinition('amqp.broker', $broker);

        $definition = $container->getDefinition('amqp.gates');

        foreach ($config['gates'] as $name => $gate) {
           $gate = new Definition(Amqp\Gate::class, [$name, $gate['exchange'], $gate['queue'], $gate['routing_key']]);
           $definition->addMethodCall('add', [$gate]);
        }
    }
}

