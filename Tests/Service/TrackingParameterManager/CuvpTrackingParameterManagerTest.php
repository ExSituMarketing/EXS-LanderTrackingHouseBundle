<?php

namespace EXS\LanderTrackingChaturbateBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\CuvpTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class CuvpTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractWithoutParametersNorCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('cuvp')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('cmp')->willReturn(false)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new CuvpTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertEmpty($result);
    }

    public function testExtractWithoutParametersButCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('cuvp')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('cmp')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->has('exid')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->has('product_id')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->get('cmp')->willReturn(123)->shouldBeCalledTimes(1);
        $cookies->get('exid')->willReturn('UUID987654321')->shouldBeCalledTimes(1);
        $cookies->get('visit', 1)->willReturn('5')->shouldBeCalledTimes(1);
        $cookies->get('product_id')->willReturn('5')->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new CuvpTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(4, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('UUID987654321', $result['exid']);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals('5', $result['visit']);

        $this->assertArrayHasKey('product_id', $result);
        $this->assertEquals('5', $result['product_id']);
    }

    public function testExtractWithParameters()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('cuvp')->willReturn('123~UUID987654321~5~5')->shouldBeCalledTimes(1);

        $request->query = $query;

        $manager = new CuvpTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(4, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);

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

        $formatter = new CuvpTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cuvp', $result);
        $this->assertNull($result['cuvp']);
    }

    public function testFormatWithAllParameters()
    {
        $trackingParameters = new ParameterBag([
            'cmp' => 123,
            'exid' => 'UUID987654321',
            'visit' => 5,
            'product_id' => 5,
        ]);

        $formatter = new CuvpTrackingParameterManager();

        $result = $formatter->format($trackingParameters);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('cuvp', $result);
        $this->assertEquals('123~UUID987654321~5~5', $result['cuvp']);
    }
}
