<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CupTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class CupTrackingParameterManager implements TrackingParameterExtracterInterface, TrackingParameterFormatterInterface
{
    /**
     * cup: cmp and exid and product id with ~ as a delimiter
     *
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (
            (null !== $cup = $request->query->get('cup'))
            && (preg_match('`^(?<cmp>[a-z0-9]+)~(?<exid>[a-z0-9]+)~(?<product_id>[a-z0-9]+)$`i', $cup, $matches))
        ) {
            $trackingParameters['cmp'] = $matches['cmp'];
            $trackingParameters['exid'] = $matches['exid'];
            $trackingParameters['product_id'] = $matches['product_id'];
        } elseif (
            $request->cookies->has('cmp')
            && $request->cookies->has('exid')
            && $request->cookies->has('product_id')
        ) {
            $trackingParameters['cmp'] = $request->cookies->get('cmp');
            $trackingParameters['exid'] = $request->cookies->get('exid');
            $trackingParameters['product_id'] = $request->cookies->get('product_id');
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
            $trackingParameters->has('cmp')
            && $trackingParameters->has('exid')
            && $trackingParameters->has('product_id')
        ) {
            $cup = sprintf(
                '%s~%s~%s',
                $trackingParameters->get('cmp'),
                $trackingParameters->get('exid'),
                $trackingParameters->get('product_id')
            );
        }

        return [
            'cup' => $cup,
        ];
    }
}
