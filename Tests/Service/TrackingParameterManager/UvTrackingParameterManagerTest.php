<?php

namespace EXS\LanderTrackingChaturbateBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\UvTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class UvTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractFromQuery()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->get('uv')->willReturn('UUID987654321~5')->shouldBeCalledTimes(1);

        $manager = new UvTrackingParameterManager();

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(2, $result);

        $this->assertArrayHasKey('u', $result);
        $this->assertEquals('UUID987654321', $result['u']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(5, $result['visit']);
    }

    public function testFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag([]);

        $formatter = new UvTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('uv', $result);
        $this->assertNull($result['uv']);
    }

    public function testFormatWithAllParameters()
    {
        $trackingParameters = new ParameterBag([
            'u' => 'UUID987654321',
            'visit' => 5,
        ]);

        $formatter = new UvTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('uv', $result);
        $this->assertEquals('UUID987654321~5', $result['uv']);
    }
}
