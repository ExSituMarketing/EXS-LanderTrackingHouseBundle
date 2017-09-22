<?php

namespace EXS\LanderTrackingChaturbateBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\CupTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class CupTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractFromQuery()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->get('cup')->willReturn('123~UUID987654321~5')->shouldBeCalledTimes(1);

        $manager = new CupTrackingParameterManager();

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('c', $result);
        $this->assertEquals(123, $result['c']);

        $this->assertArrayHasKey('u', $result);
        $this->assertEquals('UUID987654321', $result['u']);

        $this->assertArrayHasKey('p', $result);
        $this->assertEquals(5, $result['p']);
    }

    public function testFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag([]);

        $formatter = new CupTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cup', $result);
        $this->assertNull($result['cup']);
    }

    public function testFormatWithAllParameters()
    {
        $trackingParameters = new ParameterBag([
            'c' => 123,
            'u' => 'UUID987654321',
            'p' => 5,
        ]);

        $formatter = new CupTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cup', $result);
        $this->assertEquals('123~UUID987654321~5', $result['cup']);
    }
}
