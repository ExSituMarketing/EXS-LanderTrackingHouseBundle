<?php

namespace EXS\LanderTrackingChaturbateBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\CuvTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class CuvTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractFromQuery()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('cuv')->willReturn('123~UUID987654321~5')->shouldBeCalledTimes(1);

        $request->query = $query;

        $manager = new CuvTrackingParameterManager();

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('c', $result);
        $this->assertEquals(123, $result['c']);

        $this->assertArrayHasKey('u', $result);
        $this->assertEquals('UUID987654321', $result['u']);

        $this->assertArrayHasKey('v', $result);
        $this->assertEquals(5, $result['v']);
    }

    public function testFormatWithEmptyArray()
    {
        $trackingParameters = new ParameterBag([]);

        $formatter = new CuvTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cuv', $result);
        $this->assertNull($result['cuv']);
    }

    public function testFormatWithAllParameters()
    {
        $trackingParameters = new ParameterBag([
            'c' => 123,
            'u' => 'UUID987654321',
            'v' => 5,
        ]);

        $formatter = new CuvTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cuv', $result);
        $this->assertEquals('123~UUID987654321~5', $result['cuv']);
    }
}
