<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterExtracter;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\CmpTrackingParameterManager;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class TrackingParameterExtracterTest extends \PHPUnit_Framework_TestCase
{
    public function testSetupWithValidParameters()
    {
        $extracter = new TrackingParameterExtracter(1, 'exid', 1);

        $manager = $this->prophesize(CmpTrackingParameterManager::class);

        $extracters = [
            'cmp_manager' => $manager->reveal(),
        ];

        $extracter->setup($extracters);

        $this->assertAttributeCount(1, 'extracters', $extracter);
    }

    public function testExtract()
    {
        $request = $this->prophesize(Request::class);
        $revealedRequest = $request->reveal();

        $cmpManager = $this->prophesize(CmpTrackingParameterManager::class);
        $cmpManager->extract($revealedRequest)->willReturn(['cmp' => 123])->shouldBeCalledTimes(1);

        $extracter = new TrackingParameterExtracter(1, 'exid', 1);

        $reflector = new \ReflectionObject($extracter);

        $extracters = $reflector->getProperty('extracters');
        $extracters->setAccessible(true);
        $extracters->setValue($extracter, [
            'cmp_manager' => $cmpManager->reveal(),
        ]);

        $result = $extracter->extract($revealedRequest);

        $this->assertInstanceOf(ParameterBag::class, $result);
        $this->assertEquals(123, $result->get('cmp'));
        $this->assertEquals('exid', $result->get('exid'));
        $this->assertEquals(1, $result->get('visit'));
    }
}
