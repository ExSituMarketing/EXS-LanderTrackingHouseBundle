<?php

namespace EXS\LanderTrackingHouseBundle\Service;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterCookieExtracterInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterInitializerInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterQueryExtracterInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TrackingParameterExtracter
 *
 * @package EXS\LanderTrackingHouseBundle\Service
 */
class TrackingParameterExtracter
{
    /**
     * @var array
     */
    private $extracters;

    /**
     * TrackingParameterExtracter constructor.
     */
    public function __construct()
    {
        $this->extracters = [];
    }

    /**
     * Set all formatters available.
     *
     * @param array $extracters
     *
     * @throws InvalidConfigurationException
     */
    public function setup(array $extracters)
    {
        foreach ($extracters as $extracterName => $extracter) {
            if (
                (false === ($extracter instanceof TrackingParameterQueryExtracterInterface))
                && (false === ($extracter instanceof TrackingParameterCookieExtracterInterface))
                && (false === ($extracter instanceof TrackingParameterInitializerInterface))
            ) {
                throw new InvalidConfigurationException(sprintf(
                    'Invalid tracking parameter extracter "%s".',
                    $extracterName
                ));
            }
        }

        $this->extracters = $extracters;
    }

    /**
     * Extract tracking parameters from query or from cookies.
     *
     * @param Request $request
     *
     * @return ParameterBag
     */
    public function extract(Request $request)
    {
        $trackingParameters = new ParameterBag();

        /** First search the query. */
        foreach ($this->extracters as $extracter) {
            if ($extracter instanceof TrackingParameterQueryExtracterInterface) {
                $trackingParameters->add($extracter->extractFromQuery($request->query));
            }
        }

        /** The search the cookies. */
        foreach ($this->extracters as $extracter) {
            if ($extracter instanceof TrackingParameterCookieExtracterInterface) {
                $trackingParameters->add($extracter->extractFromCookies($request->cookies));
            }
        }

        /** Set default value if not found. */
        foreach ($this->extracters as $extracter) {
            if ($extracter instanceof TrackingParameterInitializerInterface) {
                $trackingParameters->add($extracter->initialize());
            }
        }

        return $trackingParameters;
    }
}
