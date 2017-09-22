<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\VisitTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class VisitTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractWithParameters()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->get('v')->willReturn(5)->shouldBeCalledTimes(1);

        $manager = new VisitTrackingParameterManager(1);

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('v', $result);
        $this->assertEquals(5, $result['v']);
    }

    public function testExtractWithoutParametersButCookies()
    {
        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->get('v')->willReturn(5)->shouldBeCalledTimes(1);

        $manager = new VisitTrackingParameterManager(1);

        $result = $manager->extractFromCookies($cookies->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('v', $result);
        $this->assertEquals(5, $result['v']);
    }

    public function testInitialise()
    {
        $manager = new VisitTrackingParameterManager(1);

        $result = $manager->initialize();

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('v', $result);
        $this->assertEquals(1, $result['v']);
    }
}
