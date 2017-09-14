<?php

namespace EXS\LanderTrackingHouseBundle\Service;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterExtracterInterface;
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
     * TrackingParameterExtractor constructor.
     */
    public function __construct()
    {
        $this->extracters = [];
    }

    /**
     * @param array $extracters
     *
     * @throws InvalidConfigurationException
     */
    public function setup(array $extracters)
    {
        foreach ($extracters as $extracterName => $extracter) {
            if (!$extracter instanceof TrackingParameterExtracterInterface) {
                throw new InvalidConfigurationException(sprintf('Invalid tracking parameter extracter "%s".', $extracterName));
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

        foreach ($this->extracters as $extracter) {
            /** @param TrackingParameterExtracterInterface $extracter */
            $trackingParameters->add($extracter->extract($request));
        }

        return $trackingParameters;
    }
}
