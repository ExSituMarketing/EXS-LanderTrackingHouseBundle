<?php

namespace EXS\LanderTrackingHouseBundle\Service\Twig;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterAppender;

/**
 * Class TrackingParameterExtension
 *
 * @package EXS\LanderTrackingHouseBundle\Service\Twig
 */
class TrackingParameterExtension extends \Twig_Extension
{
    /**
     * @var TrackingParameterAppender
     */
    private $appender;

    public function __construct(TrackingParameterAppender $appender)
    {
        $this->appender = $appender;
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('getTracking', [$this, 'getTracking']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('appendTracking', [$this, 'appendTracking']),
        ];
    }

    /**
     * @param string $url
     * @param string $formatterName
     *
     * @return string
     */
    public function appendTracking($url, $formatterName = null)
    {
        return $this->appender->append($url, $formatterName);
    }

    /**
     * @param string $parameterName
     *
     * @return string
     */
    public function getTracking($parameterName)
    {
        return $this->appender->getTrackingParameter($parameterName);
    }
}
