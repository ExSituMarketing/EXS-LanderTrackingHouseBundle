<?php

namespace EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class CmpTrackingParameterManager
 *
 * @package EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager
 */
class CmpTrackingParameterManager implements TrackingParameterQueryExtracterInterface, TrackingParameterCookieExtracterInterface, TrackingParameterInitializerInterface
{
    /**
     * @var string
     */
    private $defaultCmp;

    /**
     * CmpTrackingParameterManager constructor.
     *
     * @param $defaultCmp
     */
    public function __construct($defaultCmp)
    {
        $this->defaultCmp = $defaultCmp;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (null !== $cmp = $query->get('cmp')) {
            $trackingParameters['cmp'] = $cmp;
        }

        return $trackingParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromCookies(ParameterBag $cookies)
    {
        $trackingParameters = [];

        if (null !== $cmp = $cookies->get('cmp')) {
            $trackingParameters['cmp'] = $cmp;
        }

        return $trackingParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        return [
            'cmp' => $this->defaultCmp,
        ];
    }
}
