<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\ProductIdTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ProductIdTrackingParameterFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractWithoutParametersNorCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('p')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('product_id')->willReturn(false)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new ProductIdTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertEmpty($result);
    }

    public function testExtractWithoutParametersButCookies()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('p')->willReturn(null)->shouldBeCalledTimes(1);

        $request->query = $query;

        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->has('product_id')->willReturn(true)->shouldBeCalledTimes(1);
        $cookies->get('product_id')->willReturn(5)->shouldBeCalledTimes(1);

        $request->cookies = $cookies;

        $manager = new ProductIdTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('product_id', $result);
        $this->assertEquals(5, $result['product_id']);
    }

    public function testExtractWithParameters()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $query->get('p')->willReturn(5)->shouldBeCalledTimes(1);

        $request->query = $query;

        $manager = new ProductIdTrackingParameterManager();

        $result = $manager->extract($request->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('product_id', $result);
        $this->assertEquals(5, $result['product_id']);
    }
}
