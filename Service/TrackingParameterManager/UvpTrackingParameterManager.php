<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UvpTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class UvpTrackingParameterManager implements TrackingParameterExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * uvp: exid and visit and product id with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (
            (null !== $uvp = $request->query->get('uvp'))
            && (preg_match('`^(?<exid>[a-z0-9]+)~(?<visit>[a-z0-9]+)~(?<product_id>[a-z0-9]+)$`i', $uvp, $matches))
        ) {
            $trackingParameters['exid'] = $matches['exid'];
            $trackingParameters['visit'] = $matches['visit'];
            $trackingParameters['product_id'] = $matches['product_id'];
        } elseif (
            $request->cookies->has('exid')
            && $request->cookies->has('product_id')
        ) {
            $trackingParameters['exid'] = $request->cookies->get('exid');
            $trackingParameters['visit'] = $request->cookies->get('visit', 1);
            $trackingParameters['product_id'] = $request->cookies->get('product_id');
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
            && $trackingParameters->has('product_id')
        ) {
            $uvp = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('exid'),
                $trackingParameters->get('visit', 1),
                $trackingParameters->get('product_id')
            );
        }

        return [
            'uvp' => $uvp,
        ];
    }
}
