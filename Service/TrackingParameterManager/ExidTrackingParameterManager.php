<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class ExidTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class ExidTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterCookieExtracterInterface, TrackingParameterInitializerInterface
{
    /**
     * @var string
     */
    private $defaultExid;

    /**
     * ExidTrackingParameterManager constructor.
     *
     * @param $defaultExid
     */
    public function __construct($defaultExid)
    {
        $this->defaultExid = $defaultExid;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        $foundParameter = array_intersect(['exid', 'u', 'uuid'], $query->keys());
        if (count($foundParameter) >= 1) {
            $trackingParameters['u'] = $query->get(current($foundParameter));
        }

        return $trackingParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromCookies(ParameterBag $cookies)
    {
        $trackingParameters = [];

        $foundParameter = array_intersect(['exid', 'u', 'uuid'], $cookies->keys());
        if (count($foundParameter) >= 1) {
            $trackingParameters['u'] = $cookies->get(current($foundParameter));
        }

        return $trackingParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return [
            'u' => $this->defaultExid,
        ];
    }
}
