<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service\TrackingParameterManager;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\ProductIdTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;

class ProductIdTrackingParameterFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractFromQuery()
    {
        $query = $this->prophesize(ParameterBag::class);
        $query->get('p')->willReturn(5)->shouldBeCalledTimes(1);

        $manager = new ProductIdTrackingParameterManager();

        $result = $manager->extractFromQuery($query->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('product_id', $result);
        $this->assertEquals(5, $result['product_id']);
    }

    public function testExtractFromCookies()
    {
        $cookies = $this->prophesize(ParameterBag::class);
        $cookies->get('product_id')->willReturn(5)->shouldBeCalledTimes(1);

        $manager = new ProductIdTrackingParameterManager();

        $result = $manager->extractFromCookies($cookies->reveal());

        $this->assertCount(1, $result);

        $this->assertArrayHasKey('product_id', $result);
        $this->assertEquals(5, $result['product_id']);
    }
}
