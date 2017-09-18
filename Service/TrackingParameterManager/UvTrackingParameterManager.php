<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UvTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class UvTrackingParameterManager implements TrackingParameterExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * uv: exid and visit with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (
            (null !== $uv = $request->query->get('uv'))
            && (preg_match('`^(?<exid>[a-z0-9]+)~(?<visit>[a-z0-9]+)$`i', $uv, $matches))
        ) {
            $trackingParameters['exid'] = $matches['exid'];
            $trackingParameters['visit'] = $matches['visit'];
        } elseif (
            $request->cookies->has('exid')
            && $request->cookies->has('visit')
        ) {
            $trackingParameters['exid'] = $request->cookies->get('exid');
            $trackingParameters['visit'] = $request->cookies->get('visit');
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
