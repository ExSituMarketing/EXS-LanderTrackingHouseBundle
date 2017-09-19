<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class ProductIdTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class ProductIdTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterCookieExtracterInterface
{
    /**
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (null !== $productId = $query->get('p')) {
            $trackingParameters['product_id'] = $productId;
        }

        return $trackingParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromCookies(ParameterBag $cookies)
    {
        $trackingParameters = [];

        if (null !== $productId = $cookies->get('product_id')) {
            $trackingParameters['product_id'] = $productId;
        }

        return $trackingParameters;
    }
}
