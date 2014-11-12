<?php

namespace Ihsan\MalesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ihsan_males');

        $rootNode
            ->children()
                ->scalarNode('guesser')
                    ->defaultValue('Ihsan\MalesBundle\Guesser\BundleGuesser')
                ->end()
            ->end();

        return $treeBuilder;
    }
} 