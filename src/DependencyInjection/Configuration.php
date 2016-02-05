<?php

namespace Bare\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Application configuration tree
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Configuration implements ConfigurationInterface
{
    /** {@inheritDoc} */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('converter');

        $amqp = $root
            ->children()
                ->arrayNode('amqp')
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->info('Amqp configuration');

        $this->addAmqpNode($amqp);
        $root = $amqp->end();

        return $treeBuilder;
    }

    private function addAmqpNode(NodeDefinition $root)
    {
        $root
            ->children()
                ->arrayNode('connection')
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->info('Connection to AMQP to use')
                    ->children()
                        ->scalarNode('host')->isRequired()->end()
                        ->integerNode('port')->isRequired()->end()
                        ->scalarNode('login')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                        ->scalarNode('virtual_host')->defaultValue('/')->end()
                    ->end()
                ->end()

                ->arrayNode('gates')
                    ->canBeUnset()
                    ->useAttributeAsKey('name')
                    ->info('Access gate for each dialog with AMQP')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('exchange')
                                ->isRequired()
                                ->info('Exchange point associated to this gate')
                            ->end()

                            ->scalarNode('queue')
                                ->isRequired()
                                ->info('Queue to fetch the information from')
                            ->end()

                            ->scalarNode('routing_key')
                                ->defaultNull()
                                ->info('Routing key to use when sending messages through this gate')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
