<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class CmpTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class CmpTrackingParameterManager implements TrackingParameterExtracterInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (null !== $cmp = $request->query->get('cmp')) {
            $trackingParameters['cmp'] = $cmp;
        } elseif ($request->cookies->has('cmp')) {
            $trackingParameters['cmp'] = $request->cookies->get('cmp');
        }

        return $trackingParameters;
    }
}
