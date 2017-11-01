<?php

namespace EXS\LanderTrackingChaturbateBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\CuTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class CuTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractFromQuery()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->get('cu')->willReturn('123~UUID987654321')->shouldBeCalledTimes(1);

        $manager = new CuTrackingParameterManager();

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(2, $result);

        $this->assertArrayHasKey('c', $result);
        $this->assertEquals(123, $result['c']);

        $this->assertArrayHasKey('u', $result);
        $this->assertEquals('UUID987654321', $result['u']);
    }


    public function testFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag([]);

        $formatter = new CuTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cu', $result);
        $this->assertNull($result['cu']);
    }

    public function testFormatWithAllParameters()
    {
        $trackingParameters = new ParameterBag([
            'c' => 123,
            'u' => 'UUID987654321',
        ]);

        $formatter = new CuTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cu', $result);
        $this->assertEquals('123~UUID987654321', $result['cu']);
    }

    public function testCheckFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag([]);

        $formatter = new CuTrackingParameterManager();

        $result = $formatter->checkFormat($trackingParameters);
    }
}
