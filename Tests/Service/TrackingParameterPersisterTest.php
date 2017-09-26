<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterPersister;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class TrackingParameterPersisterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllTrackingParameters()
    {
        $persister = new TrackingParameterPersister();

        $reflector = new \ReflectionObject($persister);

        $trackingParameters = $reflector->getProperty('defaultTrackingParameters');
        $trackingParameters->setAccessible(true);
        $trackingParameters->setValue($persister, new ParameterBag([
            'c' => 123,
            'u' => 'defaultExid',
        ]));

        $trackingParameters = $reflector->getProperty('trackingParameters');
        $trackingParameters->setAccessible(true);
        $trackingParameters->setValue($persister, new ParameterBag([
            'u' => 'UUID0123456789',
            'v' => 5,
        ]));

        $result = $persister->getAllTrackingParameters();

        $this->assertInstanceOf(ParameterBag::class, $result);
        $this->assertEquals(123, $result->get('c'));
        $this->assertEquals('UUID0123456789', $result->get('u'));
        $this->assertEquals(5, $result->get('v'));
    }

    public function testGetDefaultTrackingParameters()
    {
        $persister = new TrackingParameterPersister();

        $reflector = new \ReflectionObject($persister);
        $trackingParameters = $reflector->getProperty('defaultTrackingParameters');
        $trackingParameters->setAccessible(true);
        $trackingParameters->setValue($persister, new ParameterBag([
            'c' => 123,
            'u' => 'UUID0123456789',
        ]));

        $result = $persister->getDefaultTrackingParameters();

        $this->assertInstanceOf(ParameterBag::class, $result);
        $this->assertEquals(123, $result->get('c'));
        $this->assertEquals('UUID0123456789', $result->get('u'));
    }

    public function testSetDefaultTrackingParameters()
    {
        $trackingParameters = new ParameterBag([
            'c' => 123,
            'u' => 'UUID0123456789',
        ]);

        $persister = new TrackingParameterPersister();
        $persister->setDefaultTrackingParameters($trackingParameters);

        $this->assertAttributeCount(2, 'defaultTrackingParameters', $persister);
        $this->assertAttributeInstanceOf(ParameterBag::class, 'defaultTrackingParameters', $persister);
        $this->assertAttributeEquals($trackingParameters, 'defaultTrackingParameters', $persister);
    }

    public function testGetTrackingParameters()
    {
        $persister = new TrackingParameterPersister();

        $reflector = new \ReflectionObject($persister);
        $trackingParameters = $reflector->getProperty('trackingParameters');
        $trackingParameters->setAccessible(true);
        $trackingParameters->setValue($persister, new ParameterBag([
            'c' => 123,
            'u' => 'UUID0123456789',
            'v' => null,
            'foreign_id' => null,
        ]));

        $result = $persister->getTrackingParameters();

        $this->assertInstanceOf(ParameterBag::class, $result);
        $this->assertEquals(123, $result->get('c'));
        $this->assertEquals('UUID0123456789', $result->get('u'));
    }

    public function testSetTrackingParameters()
    {
        $trackingParameters = new ParameterBag([
            'c' => 123,
            'u' => 'UUID0123456789',
            'v' => null,
            'foreign_id' => null,
        ]);

        $persister = new TrackingParameterPersister();
        $persister->setTrackingParameters($trackingParameters);

        $this->assertAttributeCount(4, 'trackingParameters', $persister);
        $this->assertAttributeInstanceOf(ParameterBag::class, 'trackingParameters', $persister);
        $this->assertAttributeEquals($trackingParameters, 'trackingParameters', $persister);
    }

    public function testPersist()
    {
        $response = $this->prophesize(Response::class);

        $headers = $this->prophesize(ResponseHeaderBag::class);
        $headers->setCookie(new Cookie('c', 123, new \DateTime('+1 year'), '/', null, false, false))->shouldBeCalledTimes(1);
        $headers->setCookie(new Cookie('v', 5, new \DateTime('+1 year'), '/', null, false, false))->shouldBeCalledTimes(1);

        $response->headers = $headers;

        $persister = new TrackingParameterPersister();

        $extracters = $this->prophesize(ParameterBag::class);
        $extracters->all()->willReturn([
            'c' => 123,
            'u' => 'UUID0123456789',
            'v' => 5,
        ])->shouldBeCalledTimes(1);

        $reflector = new \ReflectionObject($persister);

        $trackingParameters = $reflector->getProperty('trackingParameters');
        $trackingParameters->setAccessible(true);
        $trackingParameters->setValue($persister, $extracters->reveal());

        $persister->persist($response->reveal());
    }
}
