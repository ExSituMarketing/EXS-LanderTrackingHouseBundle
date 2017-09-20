<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\ExidTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class ExidTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractFromQuery()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->keys()->willReturn(['foo', 'uuid', 'bar'])->shouldBeCalledTimes(1);
        $query->get('uuid')->willReturn('EXID0123456789')->shouldBeCalledTimes(1);

        $manager = new ExidTrackingParameterManager(1);

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('EXID0123456789', $result['exid']);
    }

    public function testExtractFromCookies()
    {
        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->keys()->willReturn(['foo', 'u', 'bar'])->shouldBeCalledTimes(1);
        $cookies->get('u')->willReturn('EXID0123456789')->shouldBeCalledTimes(1);

        $manager = new ExidTrackingParameterManager(1);

        $result = $manager->extractFromCookies($cookies->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('EXID0123456789', $result['exid']);
    }

    public function testInitialize()
    {
        $manager = new ExidTrackingParameterManager(1);

        $result = $manager->initialize();

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals(1, $result['exid']);
    }
}
