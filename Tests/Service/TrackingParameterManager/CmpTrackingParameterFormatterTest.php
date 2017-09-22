<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\CmpTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class CmpTrackingParameterFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractFromQuery()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->get('c')->willReturn(null, 123)->shouldBeCalledTimes(2);

        $manager = new CmpTrackingParameterManager(1);

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertEmpty($result);

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('c', $result);
        $this->assertEquals(123, $result['c']);
    }

    public function testExtractFromCookies()
    {
        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->get('c')->willReturn(null, 123)->shouldBeCalledTimes(2);

        $manager = new CmpTrackingParameterManager(1);

        $result = $manager->extractFromCookies($cookies->reveal());

        $this->assertEmpty($result);

        $result = $manager->extractFromCookies($cookies->reveal());

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('c', $result);
        $this->assertEquals(123, $result['c']);
    }

    public function testInitialize()
    {
        $manager = new CmpTrackingParameterManager(1);

        $result = $manager->initialize();

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('c', $result);
        $this->assertEquals(1, $result['c']);
    }
}
