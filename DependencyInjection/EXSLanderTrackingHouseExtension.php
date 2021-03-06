<?php

namespace EXS\LanderTrackingHouseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class EXSLanderTrackingHouseExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('exs_lander_tracking_house.default_cmp', $config['default_cmp']);
        $container->setParameter('exs_lander_tracking_house.default_exid', $config['default_exid']);
        $container->setParameter('exs_lander_tracking_house.default_visit', $config['default_visit']);
    }
}
