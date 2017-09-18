<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class VisitTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class VisitTrackingParameterManager implements TrackingParameterExtracterInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (null !== $visit = $request->query->get('visit')) {
            $trackingParameters['visit'] = $visit;
        } else {
            $trackingParameters['visit'] = $request->cookies->get('visit', 1);
        }

        return $trackingParameters;
    }
}
