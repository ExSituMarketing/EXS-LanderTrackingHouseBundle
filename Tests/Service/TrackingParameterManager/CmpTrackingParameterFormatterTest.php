<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\CmpTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class CmpTrackingParameterFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractWithoutParametersNorCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('cmp')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('cmp')->willReturn(false)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new CmpTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertEmpty($result);
    }

    public function testExtractWithoutParametersButCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('cmp')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('cmp')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->get('cmp')->willReturn(123)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new CmpTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);
    }

    public function testExtractWithParameters()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('cmp')->willReturn(123)->shouldBeCalledTimes(1);

        $request->query = $query;

        $manager = new CmpTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('cmp', $result);
        $this->assertEquals(123, $result['cmp']);
    }
}
