<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProductIdTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class ProductIdTrackingParameterManager implements TrackingParameterExtracterInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (null !== $productId = $request->query->get('p')) {
            $trackingParameters['product_id'] = $productId;
        } elseif ($request->cookies->has('product_id')) {
            $trackingParameters['product_id'] = $request->cookies->get('product_id');
        }

        return $trackingParameters;
    }
}
