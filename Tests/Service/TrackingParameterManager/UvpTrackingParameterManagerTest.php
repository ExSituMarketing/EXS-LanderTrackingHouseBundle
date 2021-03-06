<?php

namespace EXS\LanderTrackingChaturbateBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\UvpTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class UvpTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractWithParameters()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->get('uvp')->willReturn('UUID987654321~5~5')->shouldBeCalledTimes(1);

        $manager = new UvpTrackingParameterManager();

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('u', $result);
        $this->assertEquals('UUID987654321', $result['u']);

        $this->assertArrayHasKey('v', $result);
        $this->assertEquals(5, $result['v']);

        $this->assertArrayHasKey('p', $result);
        $this->assertEquals('5', $result['p']);
    }

    public function testFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag([]);

        $formatter = new UvpTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('uvp', $result);
        $this->assertNull($result['uvp']);
    }

    public function testFormatWithAllParameters()
    {
        $trackingParameters = new ParameterBag([
            'u' => 'UUID987654321',
            'v' => 5,
            'p' => 5,
        ]);

        $formatter = new UvpTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('uvp', $result);
        $this->assertEquals('UUID987654321~5~5', $result['uvp']);
    }

    public function testCheckFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag([]);

        $formatter = new UvpTrackingParameterManager();

        $result = $formatter->checkFormat($trackingParameters);
    }
}
