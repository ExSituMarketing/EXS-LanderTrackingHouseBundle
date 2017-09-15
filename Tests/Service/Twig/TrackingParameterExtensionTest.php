<?php

namespace EXS\LanderTrackingHouseBundle\Tests\Service\Twig;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterAppender;
use EXS\LanderTrackingHouseBundle\Service\Twig\TrackingParameterExtension;

class TrackingParameterExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFilters()
    {
        $appender = $this->prophesize(TrackingParameterAppender::class);

        $extension = new TrackingParameterExtension($appender->reveal());

        $result = $extension->getFilters();

        $this->assertCount(1, $result);
        $this->assertEquals('appendTracking', $result[0]->getName());
    }

    public function testAppendTracking()
    {
        $appender = $this->prophesize(TrackingParameterAppender::class);
        $appender->append('https://www.simple.tld/foo', null)->willReturn('https://www.simple.tld/foo?track=123~UUID0123456789~5')->shouldBeCalledTimes(1);
        $appender->append('https://www.simple.tld/foo', 'foo')->willReturn('https://www.simple.tld/foo?track=123~UUID0123456789~5')->shouldBeCalledTimes(1);

        $extension = new TrackingParameterExtension($appender->reveal());

        $result = $extension->appendTracking('https://www.simple.tld/foo');

        $this->assertEquals('https://www.simple.tld/foo?track=123~UUID0123456789~5', $result);

        $result = $extension->appendTracking('https://www.simple.tld/foo', 'foo');

        $this->assertEquals('https://www.simple.tld/foo?track=123~UUID0123456789~5', $result);
    }
}
