<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class CuvTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class CuvTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * cuv: cmp and exid and visit with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (
            (null !== $cuv = $query->get('cuv'))
            && (preg_match('`^(?<c>[a-z0-9]+)~(?<u>[a-z0-9]+)~(?<v>[a-z0-9]+)$`i', $cuv, $matches))
        ) {
            $trackingParameters['c'] = $matches['c'];
            $trackingParameters['u'] = $matches['u'];
            $trackingParameters['v'] = $matches['v'];
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
            $trackingParameters->has('c')
            && $trackingParameters->has('u')
            && $trackingParameters->has('v')
        ) {
            $cuv = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('c'),
                $trackingParameters->get('u'),
                $trackingParameters->get('v')
            );
        }

        return [
            'cuv' => $cuv,
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
