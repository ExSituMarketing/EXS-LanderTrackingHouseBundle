<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterExtracter;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\CmpTrackingParameterManager;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class TrackingParameterExtracterTest extends \PHPUnit_Framework_TestCase
{
    public function testSetupWithBadParameters()
    {
        $extracter = new TrackingParameterExtracter();

        $manager = $this->prophesize(\stdClass::class);

        $extracters = [
            [
                'name' => 'invalid_manager',
                'reference' => $manager->reveal(),
            ],
        ];

        $this->setExpectedException(InvalidConfigurationException::class, 'Invalid tracking parameter extracter "invalid_manager".');

        $extracter->setup($extracters);
    }

    public function testSetupWithValidParameters()
    {
        $extracter = new TrackingParameterExtracter();

        $manager = $this->prophesize(CmpTrackingParameterManager::class);

        $extracters = [
            [
                'name' => 'cmp_manager',
                'reference' => $manager->reveal(),
            ],
        ];

        $extracter->setup($extracters);

        $this->assertAttributeCount(1, 'extracters', $extracter);
    }

    public function testExtract()
    {
        $request = $this->prophesize(Request::class);

        $query = $this->prophesize(ParameterBag::class);
        $request->query = $query->reveal();

        $cookies = $this->prophesize(ParameterBag::class);
        $request->cookies = $cookies->reveal();

        $revealedRequest = $request->reveal();

        $cmpManager = $this->prophesize(CmpTrackingParameterManager::class);
        $cmpManager->extractFromCookies($revealedRequest->cookies)->willReturn([])->shouldBeCalledTimes(1);
        $cmpManager->extractFromQuery($revealedRequest->query)->willReturn(['cmp' => 123])->shouldBeCalledTimes(1);

        $extracter = new TrackingParameterExtracter();

        $reflector = new \ReflectionObject($extracter);

        $extracters = $reflector->getProperty('extracters');
        $extracters->setAccessible(true);
        $extracters->setValue($extracter, [
            'cmp_manager' => $cmpManager->reveal(),
        ]);

        $result = $extracter->extract($revealedRequest);

        $this->assertInstanceOf(ParameterBag::class, $result);
        $this->assertEquals(123, $result->get('cmp'));
    }

    public function testGetDefaultValues()
    {
        $cmpManager = $this->prophesize(CmpTrackingParameterManager::class);
        $cmpManager->initialize()->willReturn(['cmp' => 1])->shouldBeCalledTimes(1);

        $extracter = new TrackingParameterExtracter();

        $reflector = new \ReflectionObject($extracter);

        $extracters = $reflector->getProperty('extracters');
        $extracters->setAccessible(true);
        $extracters->setValue($extracter, [
            'cmp_manager' => $cmpManager->reveal(),
        ]);

        $result = $extracter->getDefaultValues();

        $this->assertInstanceOf(ParameterBag::class, $result);
        $this->assertEquals(1, $result->get('cmp'));
    }
}
