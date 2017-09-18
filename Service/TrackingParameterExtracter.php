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
     * @var string
     */
    private $defaultCmp;

    /**
     * @var string
     */
    private $defaultExid;

    /**
     * @var int
     */
    private $defaultVisit;

    /**
     * @var array
     */
    private $extracters;

    /**
     * TrackingParameterExtracter constructor.
     *
     * @param string $defaultCmp
     * @param string $defaultExid
     * @param int    $defaultVisit
     */
    public function __construct($defaultCmp, $defaultExid, $defaultVisit)
    {
        $this->defaultCmp = $defaultCmp;
        $this->defaultExid = $defaultExid;
        $this->defaultVisit = $defaultVisit;
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

        if (null !== $this->defaultCmp) {
            $trackingParameters->set('cmp', $this->defaultCmp);
        }

        if (null !== $this->defaultExid) {
            $trackingParameters->set('exid', $this->defaultExid);
        }

        if (null !== $this->defaultVisit) {
            $trackingParameters->set('visit', $this->defaultVisit);
        }

        foreach ($this->extracters as $extracter) {
            /** @param TrackingParameterExtracterInterface $extracter */
            $trackingParameters->add($extracter->extract($request));
        }

        return $trackingParameters;
    }
}
