<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface TrackingParameterExtracterInterface
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
interface TrackingParameterExtracterInterface
{
    /**
     * Searches
     *
     * @param Request $request
     *
     * @return array
     */
    public function extract(Request $request);
}
