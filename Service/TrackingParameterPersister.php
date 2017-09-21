<?php

namespace EXS\LanderTrackingHouseBundle\Service;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TrackingParameterPersister
 *
 * @package EXS\LanderTrackingHouseBundle\Service
 */
class TrackingParameterPersister
{
    /**
     * @var ParameterBag
     */
    private $defaultTrackingParameters;

    /**
     * @var ParameterBag
     */
    private $trackingParameters;

    /**
     * TrackingParameterPersister constructor.
     */
    public function __construct()
    {
        $this->defaultTrackingParameters = new ParameterBag();
        $this->trackingParameters = new ParameterBag();
    }

    /**
     * Merge
     *
     * @return ParameterBag
     */
    public function getAllTrackingParameters()
    {
        $trackingParameters = clone $this->defaultTrackingParameters;

        $trackingParameters->add($this->trackingParameters->all());

        return $trackingParameters;
    }

    /**
     * @return ParameterBag
     */
    public function getDefaultTrackingParameters()
    {
        return $this->defaultTrackingParameters;
    }

    /**
     * @param ParameterBag $defaultTrackingParameters
     */
    public function setDefaultTrackingParameters(ParameterBag $defaultTrackingParameters)
    {
        $this->defaultTrackingParameters = $defaultTrackingParameters;
    }

    /**
     * @return ParameterBag
     */
    public function getTrackingParameters()
    {
        return $this->trackingParameters;
    }

    /**
     * @param ParameterBag $trackingParameters
     */
    public function setTrackingParameters(ParameterBag $trackingParameters)
    {
        $this->trackingParameters = $trackingParameters;
    }

    /**
     * Persists tracking parameters in cookies.
     *
     * @param Response $response
     *
     * @return Response
     */
    public function persist(Response $response)
    {
        $trackingParameters = $this->trackingParameters->all();

        foreach ($trackingParameters as $trackingParameter => $value) {
            $response->headers->setCookie(new Cookie($trackingParameter, $value));
        }

        return $response;
    }
}
