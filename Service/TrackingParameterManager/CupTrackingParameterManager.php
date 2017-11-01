<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class CupTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class CupTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * cup: cmp and exid and product id with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (
            (null !== $cup = $query->get('cup'))
            && (preg_match('`^(?<c>[a-z0-9]+)~(?<u>[a-z0-9]+)~(?<p>[a-z0-9]+)$`i', $cup, $matches))
        ) {
            $trackingParameters['c'] = $matches['c'];
            $trackingParameters['u'] = $matches['u'];
            $trackingParameters['p'] = $matches['p'];
        }

        return $trackingParameters;
    }

    /**
     * cup: cmp and exid and product id with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function format(ParameterBag $trackingParameters)
    {
        $cup = null;

        if (
            $trackingParameters->has('c')
            && $trackingParameters->has('u')
            && $trackingParameters->has('p')
        ) {
            $cup = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('c'),
                $trackingParameters->get('u'),
                $trackingParameters->get('p')
            );
        }

        return [
            'cup' => $cup,
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
