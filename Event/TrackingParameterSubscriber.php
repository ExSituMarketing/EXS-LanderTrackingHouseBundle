<?php

namespace EXS\LanderTrackingHouseBundle\Event;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterExtracter;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterPersister;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterAppender;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class TrackingParameterSubscriber
 *
 * @package EXS\LanderTrackingHouseBundle\Event
 */
class TrackingParameterSubscriber implements EventSubscriberInterface
{
    /**
     * @var TrackingParameterExtracter
     */
    private $extracter;

    /**
     * @var TrackingParameterPersister
     */
    private $persister;

    /**
     * @var TrackingParameterAppender
     */
    private $appender;

    /**
     * TrackingParameterSubscriber constructor.
     *
     * @param TrackingParameterExtracter $extracter
     * @param TrackingParameterPersister $persister
     * @param TrackingParameterAppender  $appender
     */
    public function __construct(
        TrackingParameterExtracter $extracter,
        TrackingParameterPersister $persister,
        TrackingParameterAppender $appender
    ) {
        $this->extracter = $extracter;
        $this->persister = $persister;
        $this->appender = $appender;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => [
                ['extractTrackingParameters', 0],
            ],
            KernelEvents::RESPONSE => [
                ['persistTrackingParameters', 0],
            ],
        );
    }

    /**
     * @param GetResponseEvent $event
     */
    public function extractTrackingParameters(GetResponseEvent $event)
    {
        if (true === $event->isMasterRequest()) {
            $trackingParameters = $this->extracter->extract($event->getRequest());
            $this->persister->setTrackingParameters($trackingParameters);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function persistTrackingParameters(FilterResponseEvent $event)
    {
        $this->persister->persist($event->getResponse());
    }
}
