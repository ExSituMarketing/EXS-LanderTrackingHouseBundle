<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class UvpTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class UvpTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * uvp: exid and visit and product id with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (
            (null !== $uvp = $query->get('uvp'))
            && (preg_match('`^(?<u>[a-z0-9]+)~(?<v>[a-z0-9]+)~(?<p>[a-z0-9]+)$`i', $uvp, $matches))
        ) {
            $trackingParameters['u'] = $matches['u'];
            $trackingParameters['v'] = $matches['v'];
            $trackingParameters['p'] = $matches['p'];
        }

        return $trackingParameters;
    }

    /**
     * uvp: exid and visit and product id with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function format(ParameterBag $trackingParameters)
    {
        $uvp = null;

        if (
            $trackingParameters->has('u')
            && $trackingParameters->has('v')
            && $trackingParameters->has('p')
        ) {
            $uvp = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('u'),
                $trackingParameters->get('v'),
                $trackingParameters->get('p')
            );
        }

        return [
            'uvp' => $uvp,
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
