<?php

namespace Vangrg\RequestMapperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class VangrgRequestMapperExtension.
 *
 * @author Ivan Griga <grigaivan2@gmail.com>
 */
class VangrgRequestMapperExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');

        if ($config['validation_response']['enabled']) {

            $loader->load('validation_response_listener.yaml');

            $definition = $container->getDefinition('vangrg.request_mapper.prepare_validation_response');

            $definition->replaceArgument(1, $config['validation_response']['format']);
        }
    }
}