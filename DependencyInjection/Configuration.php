<?php

namespace Vangrg\RequestMapperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('vangrg_request_mapper');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('validation_response')
                    ->canBeEnabled()
                    ->beforeNormalization()
                        ->ifArray()->then(function ($v) {
                            if (!empty($v)) {
                                if (!isset($v['enabled'])) {
                                    $v['enabled'] = true;
                                }

                                if (!isset($v['format'])) {
                                    $v['format'] = 'json';
                                }
                            }

                            return $v;
                        })
                    ->end()
                    ->children()
                        ->scalarNode('format')->defaultValue('json')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}