<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class VisitTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class VisitTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterCookieExtracterInterface, TrackingParameterInitializerInterface
{
    /**
     * @var string
     */
    private $defaultVisit;

    /**
     * ExidTrackingParameterManager constructor.
     *
     * @param $defaultVisit
     */
    public function __construct($defaultVisit)
    {
        $this->defaultVisit = $defaultVisit;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (null !== $visit = $query->get('v')) {
            $trackingParameters['v'] = $visit;
        }

        return $trackingParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromCookies(ParameterBag $cookies)
    {
        $trackingParameters = [];

        if (null !== $visit = $cookies->get('v')) {
            $trackingParameters['v'] = $visit;
        }

        return $trackingParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return [
            'v' => $this->defaultVisit,
        ];
    }
}
