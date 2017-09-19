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
            && (preg_match('`^(?<exid>[a-z0-9]+)~(?<visit>[a-z0-9]+)~(?<product_id>[a-z0-9]+)$`i', $uvp, $matches))
        ) {
            $trackingParameters['exid'] = $matches['exid'];
            $trackingParameters['visit'] = $matches['visit'];
            $trackingParameters['product_id'] = $matches['product_id'];
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
            $trackingParameters->has('exid')
            && $trackingParameters->has('visit')
            && $trackingParameters->has('product_id')
        ) {
            $uvp = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('exid'),
                $trackingParameters->get('visit'),
                $trackingParameters->get('product_id')
            );
        }

        return [
            'uvp' => $uvp,
        ];
    }
}
