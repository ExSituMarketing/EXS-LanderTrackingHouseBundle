<?php

namespace EXS\LanderTrackingHouseBundle;

use EXS\LanderTrackingHouseBundle\DependencyInjection\Compiler\TrackingParameterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class EXSLanderTrackingHouseBundle
 *
 * @package EXS\LanderTrackingHouseBundle
 */
class EXSLanderTrackingHouseBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TrackingParameterPass());
    }
}
