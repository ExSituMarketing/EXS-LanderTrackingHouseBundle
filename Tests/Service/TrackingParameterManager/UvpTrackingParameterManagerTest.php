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

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('UUID987654321', $result['exid']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(5, $result['visit']);

        $this->assertArrayHasKey('product_id', $result);
        $this->assertEquals('5', $result['product_id']);
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
            'exid' => 'UUID987654321',
            'visit' => 5,
            'product_id' => 5,
        ]);

        $formatter = new UvpTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('uvp', $result);
        $this->assertEquals('UUID987654321~5~5', $result['uvp']);
    }
}
