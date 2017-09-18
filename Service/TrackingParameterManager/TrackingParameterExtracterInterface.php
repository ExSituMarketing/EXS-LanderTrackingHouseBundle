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
     * Extracts a parameter from the query.
     * Processes some change on it if needed.
     * Returns a key/value array of parameters to be stored as a cookie so it will be available for formatters.
     *
     * @param Request $request
     *
     * @return array
     */
    public function extract(Request $request);
}
