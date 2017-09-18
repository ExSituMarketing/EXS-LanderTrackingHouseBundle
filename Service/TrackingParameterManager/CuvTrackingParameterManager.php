<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CuvTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class CuvTrackingParameterManager implements TrackingParameterExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * cuv: cmp and exid and visit with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (
            (null !== $cuv = $request->query->get('cuv'))
            && (preg_match('`^(?<cmp>[a-z0-9]+)~(?<exid>[a-z0-9]+)~(?<visit>[a-z0-9]+)$`i', $cuv, $matches))
        ) {
            $trackingParameters['cmp'] = $matches['cmp'];
            $trackingParameters['exid'] = $matches['exid'];
            $trackingParameters['visit'] = $matches['visit'];
        } elseif (
            $request->cookies->has('cmp')
            && $request->cookies->has('exid')
        ) {
            $trackingParameters['cmp'] = $request->cookies->get('cmp');
            $trackingParameters['exid'] = $request->cookies->get('exid');
            $trackingParameters['visit'] = $request->cookies->get('visit', 1);
        }

        return $trackingParameters;
    }

    /**
     * cuv: cmp and exid and visit with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function format(ParameterBag $trackingParameters)
    {
        $cuv = null;

        if (
            $trackingParameters->has('cmp')
            && $trackingParameters->has('exid')
        ) {
            $cuv = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('cmp'),
                $trackingParameters->get('exid'),
                $trackingParameters->get('visit', 1)
            );
        }

        return [
            'cuv' => $cuv,
        ];
    }
}
