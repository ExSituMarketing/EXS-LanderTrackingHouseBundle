<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\ExidTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ExidTrackingParameterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractWithoutParametersNorCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->keys()->willReturn(['foo', 'bar'])->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('exid')->willReturn(false)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new ExidTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertEmpty($result);
    }

    public function testExtractWithoutParametersButCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->keys()->willReturn(['foo', 'bar'])->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('exid')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->get('exid')->willReturn('EXID0123456789')->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new ExidTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('EXID0123456789', $result['exid']);
    }

    public function testExtractWithParameters()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->keys()->willReturn(['foo', 'uuid', 'bar'])->shouldBeCalledTimes(1);
        $query->get('uuid')->willReturn('EXID0123456789')->shouldBeCalledTimes(1);

        $request->query = $query;

        $manager = new ExidTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('exid', $result);
        $this->assertEquals('EXID0123456789', $result['exid']);
    }
}
