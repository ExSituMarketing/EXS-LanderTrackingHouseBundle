<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\VisitTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class VisitTrackingParameterFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractWithoutParametersNorCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('visit')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('visit')->willReturn(false)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new VisitTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(1, $result['visit']);
    }

    public function testExtractWithoutParametersButCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('visit')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('visit')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->get('visit')->willReturn(5)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new VisitTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(5, $result['visit']);
    }

    public function testExtractWithParameters()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('visit')->willReturn(5)->shouldBeCalledTimes(1);

        $request->query = $query;

        $manager = new VisitTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('visit', $result);
        $this->assertEquals(5, $result['visit']);
    }
}
