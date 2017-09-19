<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Interface TrackingParameterQueryExtracterInterface
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
interface TrackingParameterQueryExtracterInterface
{
    /**
     * Extracts a parameter from the query.
     * Processes some change on it if needed.
     * Returns a key/value array of parameters to be stored as a cookie so it will be available for formatters.
     *
     * @param ParameterBag $query
     *
     * @return array
     */
    public function extractFromQuery(ParameterBag $query);
}
