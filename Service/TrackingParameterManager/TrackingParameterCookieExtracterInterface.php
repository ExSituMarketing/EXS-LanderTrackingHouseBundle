<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Interface TrackingParameterCookieExtracterInterface
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
interface TrackingParameterCookieExtracterInterface
{
    /**
     * Extracts a parameter from the query.
     * Processes some change on it if needed.
     * Returns a key/value array of parameters to be stored as a cookie so it will be available for formatters.
     *
     * @param ParameterBag $cookies
     *
     * @return array
     */
    public function extractFromCookies(ParameterBag $cookies);
}
