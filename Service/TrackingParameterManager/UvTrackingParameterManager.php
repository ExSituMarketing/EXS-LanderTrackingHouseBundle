<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class UvTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class UvTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * uv: exid and visit with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (
            (null !== $uv = $query->get('uv'))
            && (preg_match('`^(?<u>[a-z0-9]+)~(?<visit>[a-z0-9]+)$`i', $uv, $matches))
        ) {
            $trackingParameters['u'] = $matches['u'];
            $trackingParameters['visit'] = $matches['visit'];
        }

        return $trackingParameters;
    }

    /**
     * uv: exid and visit with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function format(ParameterBag $trackingParameters)
    {
        $uv = null;

        if (
            $trackingParameters->has('u')
            && $trackingParameters->has('visit')
        ) {
            $uv = sprintf(
                '%s~%s',
                $trackingParameters->get('u'),
                $trackingParameters->get('visit')
            );
        }

        return [
            'uv' => $uv,
        ];
    }
}
