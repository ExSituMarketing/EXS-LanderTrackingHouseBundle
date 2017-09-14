<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class ExidTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class ExidTrackingParameterManager implements TrackingParameterExtracterInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $trackingParameters = [];

        $foundParameter = array_intersect(['exid', 'u', 'uuid'], $request->query->keys());
        if (count($foundParameter) >= 1) {
            $trackingParameters['exid'] = $request->query->get(current($foundParameter));
        } elseif ($request->cookies->has('exid')) {
            $trackingParameters['exid'] = $request->cookies->get('exid');
        }

        return $trackingParameters;
    }
}
