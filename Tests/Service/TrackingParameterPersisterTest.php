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
            'cmp' => 123,
            'exid' => 'defaultExid',
        ]));

        $trackingParameters = $reflector->getProperty('trackingParameters');
        $trackingParameters->setAccessible(true);
        $trackingParameters->setValue($persister, new ParameterBag([
            'exid' => 'UUID0123456789',
            'visit' => 5,
        ]));

        $result = $persister->getAllTrackingParameters();

        $this->assertInstanceOf(ParameterBag::class, $result);
        $this->assertEquals(123, $result->get('cmp'));
        $this->assertEquals('UUID0123456789', $result->get('exid'));
        $this->assertEquals(5, $result->get('visit'));
    }

    public function testGetDefaultTrackingParameters()
    {
        $persister = new TrackingParameterPersister();

        $reflector = new \ReflectionObject($persister);
        $trackingParameters = $reflector->getProperty('defaultTrackingParameters');
        $trackingParameters->setAccessible(true);
        $trackingParameters->setValue($persister, new ParameterBag([
            'cmp' => 123,
            'exid' => 'UUID0123456789',
        ]));

        $result = $persister->getDefaultTrackingParameters();

        $this->assertInstanceOf(ParameterBag::class, $result);
        $this->assertEquals(123, $result->get('cmp'));
        $this->assertEquals('UUID0123456789', $result->get('exid'));
    }

    public function testSetDefaultTrackingParameters()
    {
        $trackingParameters = new ParameterBag([
            'cmp' => 123,
            'exid' => 'UUID0123456789',
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
            'cmp' => 123,
            'exid' => 'UUID0123456789',
            'visit' => null,
            'foreign_id' => null,
        ]));

        $result = $persister->getTrackingParameters();

        $this->assertInstanceOf(ParameterBag::class, $result);
        $this->assertEquals(123, $result->get('cmp'));
        $this->assertEquals('UUID0123456789', $result->get('exid'));
    }

    public function testSetTrackingParameters()
    {
        $trackingParameters = new ParameterBag([
            'cmp' => 123,
            'exid' => 'UUID0123456789',
            'visit' => null,
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
        $headers->setCookie(new Cookie('cmp', 123))->shouldBeCalledTimes(1);
        $headers->setCookie(new Cookie('exid', 'UUID0123456789'))->shouldBeCalledTimes(1);
        $headers->setCookie(new Cookie('visit', null))->shouldBeCalledTimes(1);
        $headers->setCookie(new Cookie('foreign_id', null))->shouldBeCalledTimes(1);

        $response->headers = $headers;

        $persister = new TrackingParameterPersister();

        $extracters = $this->prophesize(ParameterBag::class);
        $extracters->all()->willReturn([
            'cmp' => 123,
            'exid' => 'UUID0123456789',
            'visit' => null,
            'foreign_id' => null,
        ])->shouldBeCalledTimes(1);

        $reflector = new \ReflectionObject($persister);

        $trackingParameters = $reflector->getProperty('trackingParameters');
        $trackingParameters->setAccessible(true);
        $trackingParameters->setValue($persister, $extracters->reveal());

        $persister->persist($response->reveal());
    }
}
