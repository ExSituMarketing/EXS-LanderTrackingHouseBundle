<?php

namespace EXS\LanderTrackingChaturbateBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\CuvpTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class CuvpTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractFromQuery()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->get('cuvp')->willReturn('123~UUID987654321~5~5')->shouldBeCalledTimes(1);

        $manager = new CuvpTrackingParameterManager();

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(4, $result);

        $this->assertArrayHasKey('c', $result);
        $this->assertEquals(123, $result['c']);

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

        $formatter = new CuvpTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cuvp', $result);
        $this->assertNull($result['cuvp']);
    }

    public function testFormatWithAllParameters()
    {
        $trackingParameters = new ParameterBag([
            'c' => 123,
            'u' => 'UUID987654321',
            'v' => 5,
            'p' => 5,
        ]);

        $formatter = new CuvpTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cuvp', $result);
        $this->assertEquals('123~UUID987654321~5~5', $result['cuvp']);
    }
}
