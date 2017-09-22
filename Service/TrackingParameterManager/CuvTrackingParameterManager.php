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
            && (preg_match('`^(?<cmp>[a-z0-9]+)~(?<u>[a-z0-9]+)~(?<visit>[a-z0-9]+)$`i', $cuv, $matches))
        ) {
            $trackingParameters['cmp'] = $matches['cmp'];
            $trackingParameters['u'] = $matches['u'];
            $trackingParameters['visit'] = $matches['visit'];
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
            && $trackingParameters->has('u')
            && $trackingParameters->has('visit')
        ) {
            $cuv = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('cmp'),
                $trackingParameters->get('u'),
                $trackingParameters->get('visit')
            );
        }

        return [
            'cuv' => $cuv,
        ];
    }
}
