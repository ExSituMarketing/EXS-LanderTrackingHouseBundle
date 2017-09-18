<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CuTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class CuTrackingParameterManager implements TrackingParameterExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * cu: cmp and exid with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (
            (null !== $cu = $request->query->get('cu'))
            && (preg_match('`^(?<cmp>[a-z0-9]+)~(?<exid>[a-z0-9]+)$`i', $cu, $matches))
        ) {
            $trackingParameters['cmp'] = $matches['cmp'];
            $trackingParameters['exid'] = $matches['exid'];
        } elseif (
            $request->cookies->has('cmp')
            && $request->cookies->has('exid')
        ) {
            $trackingParameters['cmp'] = $request->cookies->get('cmp');
            $trackingParameters['exid'] = $request->cookies->get('exid');
        }

        return $trackingParameters;
    }

    /**
     * cu: cmp and exid with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function format(ParameterBag $trackingParameters)
    {
        $cu = null;

        if (
            $trackingParameters->has('cmp')
            && $trackingParameters->has('exid')
        ) {
            $cu = sprintf(
                '%s~%s',
                $trackingParameters->get('cmp'),
                $trackingParameters->get('exid')
            );
        }

        return [
            'cu' => $cu,
        ];
    }
}
