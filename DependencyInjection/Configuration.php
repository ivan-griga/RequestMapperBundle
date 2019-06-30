<?php

namespace Vangrg\RequestMapperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

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
                    ->canBeDisabled()
                    ->beforeNormalization()
                        ->ifArray()->then(function ($v) {
                            if (!empty($v)) {
                                if (!isset($v['enabled'])) {
                                    $v['enabled'] = true;
                                }

                                if (!isset($v['format'])) {
                                    $v['format'] = 'json';
                                }

                                if (!in_array($v['format'], ['json', 'xml'])) {
                                    throw new InvalidConfigurationException(
                                        'Invalid response format in vangrg_request_mapper.validation_response.format, valid formats: "json", "xml".'
                                    );
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