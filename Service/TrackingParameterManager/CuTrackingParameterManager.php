<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class CuTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class CuTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * cu: cmp and exid with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (
            (null !== $cu = $query->get('cu'))
            && (preg_match('`^(?<c>[a-z0-9]+)~(?<u>[a-z0-9]+)$`i', $cu, $matches))
        ) {
            $trackingParameters['c'] = $matches['c'];
            $trackingParameters['u'] = $matches['u'];
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
            $trackingParameters->has('c')
            && $trackingParameters->has('u')
        ) {
            $cu = sprintf(
                '%s~%s',
                $trackingParameters->get('c'),
                $trackingParameters->get('u')
            );
        }

        return [
            'cu' => $cu,
        ];
    }

    /**
     * @param  $parameters
     *
     * @return ParameterBag
     */
    public function checkFormat($parameters)
    {
        return $parameters;
    }
}
