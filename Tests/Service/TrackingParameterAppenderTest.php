<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterAppender;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterFormatterInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterPersister;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\ParameterBag;

class TrackingParameterAppenderTest extends \PHPUnit_Framework_TestCase
{
    public function testSetupWithValidConfig()
    {
        $persister = $this->prophesize(TrackingParameterPersister::class);

        $appender = new TrackingParameterAppender($persister->reveal());

        $someFormatter = $this->prophesize(TrackingParameterFormatterInterface::class);

        $appender->setup([
            'some_formatter' => $someFormatter->reveal(),
        ]);

        $this->assertAttributeEquals([
            'some_formatter' => $someFormatter->reveal(),
        ], 'formatters', $appender);
    }

    public function testSetupWithInvalidConfig()
    {
        $persister = $this->prophesize(TrackingParameterPersister::class);

        $appender = new TrackingParameterAppender($persister->reveal());

        $this->setExpectedException(InvalidConfigurationException::class, 'Invalid tracking parameter formatter "foo".');

        $appender->setup([
            'foo' => new \stdClass(),
        ]);
    }

    public function testBuildUrlWithEmptyParameters()
    {
        $persister = $this->prophesize(TrackingParameterPersister::class);

        $appender = new TrackingParameterAppender($persister->reveal());

        $reflection = new \ReflectionClass($appender);
        $method = $reflection->getMethod('buildUrl');
        $method->setAccessible(true);

        $result = $method->invokeArgs($appender, [[]]);

        $this->assertEmpty($result);
    }

    public function testBuildUrlWithAllParameters()
    {
        $persister = $this->prophesize(TrackingParameterPersister::class);

        $appender = new TrackingParameterAppender($persister->reveal());

        $reflection = new \ReflectionClass($appender);
        $method = $reflection->getMethod('buildUrl');
        $method->setAccessible(true);

        $result = $method->invokeArgs($appender, [[
            'scheme'   => 'https',
            'host'     => 'www.test.tld',
            'path'     => '/foo',
            'query'    => 'bar=baz',
        ]]);

        $this->assertEquals('https://www.test.tld/foo?bar=baz', $result);
    }

    public function testAppend()
    {
        $persister = $this->prophesize(TrackingParameterPersister::class);
        $persister->getAllTrackingParameters()->willReturn(
            new ParameterBag(['c' => 123]),
            new ParameterBag(['c' => 123]),
            new ParameterBag(['c' => 123]),
            new ParameterBag(),
            new ParameterBag()
        )->shouldBeCalledTimes(5);

        $appender = new TrackingParameterAppender($persister->reveal());

        $reflector = new \ReflectionObject($appender);

        $someFormatter = $this->prophesize(TrackingParameterFormatterInterface::class);
        $someFormatter->format(new ParameterBag([
            'c' => 123,
        ]))->willReturn(['some' => 123])->shouldBeCalledTimes(1);
        $someFormatter->format(new ParameterBag())->willReturn([])->shouldBeCalledTimes(1);

        $trackingParameters = $reflector->getProperty('formatters');
        $trackingParameters->setAccessible(true);
        $trackingParameters->setValue($appender, [
            'some_formatter' => $someFormatter->reveal(),
        ]);

        $result = $appender->append('https://www.test.tld/foo');
        $this->assertEquals('https://www.test.tld/foo', $result);

        $result = $appender->append('https://www.test.tld/foo?cmp={c}&bar=baz');
        $this->assertEquals('https://www.test.tld/foo?cmp=123&bar=baz', $result);

        $result = $appender->append('https://www.test.tld/foo?bar=baz', 'some');
        $this->assertEquals('https://www.test.tld/foo?bar=baz&some=123', $result);

        $result = $appender->append('https://www.test.tld/foo?bar=baz', 'some');
        $this->assertEquals('https://www.test.tld/foo?bar=baz', $result);

        $this->setExpectedException(InvalidConfigurationException::class, 'Unknown formatter "another".');

        $appender->append('https://www.test.tld/foo?bar=baz', 'another');
    }

    public function testGetTrackingParameterWithAValidParametername()
    {
        $persister = $this->prophesize(TrackingParameterPersister::class);
        $persister->getAllTrackingParameters()->willReturn(
            new ParameterBag(['foo' => 123])
        )->shouldBeCalledTimes(1);

        $appender = new TrackingParameterAppender($persister->reveal());

        $result = $appender->getTrackingParameter('foo');

        $this->assertEquals(123, $result);
    }

    public function testGetTrackingParameterWithAParameternameFromAformatter()
    {
        $persister = $this->prophesize(TrackingParameterPersister::class);
        $persister->getAllTrackingParameters()->willReturn(new ParameterBag(['foo' => 123]))->shouldBeCalledTimes(1);

        $appender = new TrackingParameterAppender($persister->reveal());

        $reflector = new \ReflectionObject($appender);

        $barFormatter = $this->prophesize(TrackingParameterFormatterInterface::class);
        $barFormatter->format(new ParameterBag(['foo' => 123]))->willReturn(['bar' => 456])->shouldBeCalledTimes(1);

        $trackingParameters = $reflector->getProperty('formatters');
        $trackingParameters->setAccessible(true);
        $trackingParameters->setValue($appender, [
            'some_formatter' => $barFormatter->reveal(),
        ]);

        $result = $appender->getTrackingParameter('bar');

        $this->assertEquals(456, $result);
    }

    public function testGetTrackingParameterWithUnknownParameterName()
    {
        $persister = $this->prophesize(TrackingParameterPersister::class);
        $persister->getAllTrackingParameters()->willReturn(new ParameterBag(['foo' => 123]))->shouldBeCalledTimes(1);

        $appender = new TrackingParameterAppender($persister->reveal());

        $result = $appender->getTrackingParameter('baz');

        $this->assertNull($result);
    }
}
