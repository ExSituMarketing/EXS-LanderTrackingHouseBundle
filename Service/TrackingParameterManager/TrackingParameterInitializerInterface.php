<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

/**
 * Interface TrackingParameterInitializerInterface
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
interface TrackingParameterInitializerInterface
{
    /**
     * Extracts a parameter from the query.
     * Processes some change on it if needed.
     * Returns a key/value array of parameters to be stored as a cookie so it will be available for formatters.
     *
     * @return array
     */
    public function initialize();
}
