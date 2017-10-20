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
        foreach ($extracters as $extracter) {
            if (
                (false === ($extracter['reference'] instanceof TrackingParameterQueryExtracterInterface))
                && (false === ($extracter['reference'] instanceof TrackingParameterCookieExtracterInterface))
                && (false === ($extracter['reference'] instanceof TrackingParameterInitializerInterface))
            ) {
                throw new InvalidConfigurationException(sprintf(
                    'Invalid tracking parameter extracter "%s".',
                    $extracter['name']
                ));
            }

            $this->extracters[$extracter['name']] = $extracter['reference'];
        }
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

        /* Get value from cookies. */
        foreach ($this->extracters as $extracter) {
            if ($extracter instanceof TrackingParameterCookieExtracterInterface) {
                $trackingParameters->add($extracter->extractFromCookies($request->cookies));
            }
        }

        /* Override cookies' value by query's value if set. */
        foreach ($this->extracters as $extracter) {
            if ($extracter instanceof TrackingParameterQueryExtracterInterface) {
                $trackingParameters->add($extracter->extractFromQuery($request->query));
            }
        }

        return $trackingParameters;
    }

    /**
     * @return ParameterBag
     */
    public function getDefaultValues()
    {
        $trackingParameters = new ParameterBag();

        foreach ($this->extracters as $extracter) {
            if ($extracter instanceof TrackingParameterInitializerInterface) {
                $trackingParameters->add($extracter->initialize());
            }
        }

        return $trackingParameters;
    }
}
