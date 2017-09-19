<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

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
            && (preg_match('`^(?<cmp>[a-z0-9]+)~(?<exid>[a-z0-9]+)~(?<visit>[a-z0-9]+)~(?<product_id>[a-z0-9]+)$`i', $cuvp, $matches))
        ) {
            $trackingParameters['cmp'] = $matches['cmp'];
            $trackingParameters['exid'] = $matches['exid'];
            $trackingParameters['visit'] = $matches['visit'];
            $trackingParameters['product_id'] = $matches['product_id'];
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
            $trackingParameters->has('cmp')
            && $trackingParameters->has('exid')
            && $trackingParameters->has('visit')
            && $trackingParameters->has('product_id')
        ) {
            $cuvp = sprintf(
                '%s~%s~%s~%s',
                $trackingParameters->get('cmp'),
                $trackingParameters->get('exid'),
                $trackingParameters->get('visit'),
                $trackingParameters->get('product_id')
            );
        }

        return [
            'cuvp' => $cuvp,
        ];
    }
}
