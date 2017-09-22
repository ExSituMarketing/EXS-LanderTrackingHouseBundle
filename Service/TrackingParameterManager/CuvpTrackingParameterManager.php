<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class CuvpTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class CuvpTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * cuvp: cmp and exid and visit and product id with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (
            (null !== $cuvp = $query->get('cuvp'))
            && (preg_match('`^(?<c>[a-z0-9]+)~(?<u>[a-z0-9]+)~(?<v>[a-z0-9]+)~(?<p>[a-z0-9]+)$`i', $cuvp, $matches))
        ) {
            $trackingParameters['c'] = $matches['c'];
            $trackingParameters['u'] = $matches['u'];
            $trackingParameters['v'] = $matches['v'];
            $trackingParameters['p'] = $matches['p'];
        }

        return $trackingParameters;
    }

    /**
     * cuvp: cmp and exid and visit and product id with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function format(ParameterBag $trackingParameters)
    {
        $cuvp = null;

        if (
            $trackingParameters->has('c')
            && $trackingParameters->has('u')
            && $trackingParameters->has('v')
            && $trackingParameters->has('p')
        ) {
            $cuvp = sprintf(
                '%s~%s~%s~%s',
                $trackingParameters->get('c'),
                $trackingParameters->get('u'),
                $trackingParameters->get('v'),
                $trackingParameters->get('p')
            );
        }

        return [
            'cuvp' => $cuvp,
        ];
    }
}
