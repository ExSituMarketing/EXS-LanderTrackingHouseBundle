<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

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
            && (preg_match('`^(?<exid>[a-z0-9]+)~(?<visit>[a-z0-9]+)$`i', $uv, $matches))
        ) {
            $trackingParameters['exid'] = $matches['exid'];
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
            $trackingParameters->has('exid')
            && $trackingParameters->has('visit')
        ) {
            $uv = sprintf(
                '%s~%s',
                $trackingParameters->get('exid'),
                $trackingParameters->get('visit')
            );
        }

        return [
            'uv' => $uv,
        ];
    }
}
