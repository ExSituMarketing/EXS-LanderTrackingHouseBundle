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
                $extracters[] = [
                    'priority' => isset($tags[0]['priority']) ? (int)$tags[0]['priority'] : 0,
                    'name' => str_replace('exs_tracking.', '', $id),
                    'reference' => new Reference($id),
                ];
            }

            usort($extracters, function ($a, $b) {
                if ($a['priority'] == $b['priority']) {
                    return 0;
                }

                return ($a['priority'] < $b['priority']) ? -1 : 1;
            });

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
