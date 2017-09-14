<?php

namespace EXS\LanderTrackingHouseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TrackingParameterPass
 *
 * @package EXS\LanderTrackingHouseBundle\DependencyInjection\Compiler
 */
class TrackingParameterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (true === $container->has('exs_tracking.tracking_parameter_extracter')) {
            $definition = $container->findDefinition('exs_tracking.tracking_parameter_extracter');

            $taggedServices = $container->findTaggedServiceIds('exs_tracking.parameter_extracter');

            $extracters = [];
            foreach ($taggedServices as $id => $tags) {
                $extracters[str_replace('exs_tracking.', '', $id)] = new Reference($id);
            }

            $definition->addMethodCall('setup', [$extracters]);
        }

        if (true === $container->has('exs_tracking.tracking_parameter_appender')) {
            $definition = $container->findDefinition('exs_tracking.tracking_parameter_appender');

            $taggedServices = $container->findTaggedServiceIds('exs_tracking.parameter_formatter');

            $formatters = [];
            foreach ($taggedServices as $id => $tags) {
                $formatters[str_replace('exs_tracking.', '', $id)] = new Reference($id);
            }

            $definition->addMethodCall('setup', [$formatters]);
        }
    }
}
