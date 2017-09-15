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
            new \Twig_SimpleFilter('attachAweTracking', [$this, 'appendAweTrackingParameter']),
            new \Twig_SimpleFilter('attachCambuilderTracking', [$this, 'appendCambuilderTrackingParameter']),
            new \Twig_SimpleFilter('attachChaturbateTracking', [$this, 'appendChaturbateTrackingParameter']),
        ];
    }

    /**
     * @param string $url
     * Awe
     * @return string
     */
    public function appendAweTrackingParameter($url)
    {
        return $this->appender->append($url, 'Awe');
    }

    /**
     * @param string $url
     * Cambuilder
     * @return string
     */
    public function appendCambuilderTrackingParameter($url)
    {
        return $this->appender->append($url, 'Cambuilder');
    }

    /**
     * @param string $url
     * Chaturbate
     * @return string
     */
    public function appendChaturbateTrackingParameter($url)
    {
        return $this->appender->append($url, 'Chaturbate');
    }
}
