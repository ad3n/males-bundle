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

        $responses = array('html', 'json', 'xml');

        $rootNode
            ->children()
                ->scalarNode('guesser')
                    ->defaultValue('Ihsan\MalesBundle\Guesser\BundleGuesser')
                ->end()
                ->scalarNode('response_type')
                    ->defaultValue('html')
                    ->validate()
                        ->ifNotInArray($responses)
                        ->thenInvalid(sprintf('The response format is not avialable. Please choose one of %s', json_encode($responses)))
                    ->end()
                    ->cannotBeOverwritten()
                ->end()
            ->end();

        return $treeBuilder;
    }
} 