<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Interface TrackingParameterFormatterInterface
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
interface TrackingParameterFormatterInterface
{
    /**
     * Receive all the tracking parameters.
     * Returns a key/value array of parameters to append to any url as query parameters.
     *
     * @param ParameterBag $trackingParameters
     *
     * @return array
     */
    public function format(ParameterBag $trackingParameters);
}
