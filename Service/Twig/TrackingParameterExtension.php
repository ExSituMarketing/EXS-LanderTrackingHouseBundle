<?php

namespace EXS\LanderTrackingHouseBundle\Service\Twig;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterAppender;

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

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('track', [$this, 'appendTrackingParameter']),
        ];
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function appendTrackingParameter($url)
    {
        return $this->appender->append($url);
    }
}
