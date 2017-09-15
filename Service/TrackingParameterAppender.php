<?php

namespace EXS\LanderTrackingHouseBundle\Service;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterFormatterInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class TrackingParameterAppender
 *
 * @package EXS\LanderTrackingHouseBundle\Service
 */
class TrackingParameterAppender
{
    /**
     * @var TrackingParameterPersister
     */
    private $persister;

    /**
     * @var array|TrackingParameterFormatterInterface[]
     */
    private $formatters;

    /**
     * TrackingParameterAppender constructor.
     *
     * @param TrackingParameterPersister $persister
     */
    public function __construct(TrackingParameterPersister $persister)
    {
        $this->persister = $persister;
        $this->formatters = [];
    }

    /**
     * @param array $formatters
     *
     * @throws InvalidConfigurationException
     */
    public function setup(array $formatters)
    {
        foreach ($formatters as $formatterName => $formatter) {
            if (!$formatter instanceof TrackingParameterFormatterInterface) {
                throw new InvalidConfigurationException(sprintf('Invalid tracking parameter formatter "%s".', $formatterName));
            }
        }

        $this->formatters = $formatters;
    }

    /**
     * Builds an urls with all components as returned by parse_url().
     *
     * @param array $urlComponents
     *
     * @return string
     */
    private function buildUrl(array $urlComponents)
    {
        return sprintf(
            '%s%s%s%s%s%s%s',
            isset($urlComponents['scheme']) ? $urlComponents['scheme'] . '://' : '',
            isset($urlComponents['user']) ? $urlComponents['user'] . (isset($urlComponents['pass']) ? ':' . $urlComponents['pass'] : '') . '@' : '',
            isset($urlComponents['host']) ? $urlComponents['host'] : '',
            isset($urlComponents['port']) ? ':' . $urlComponents['port'] : '',
            isset($urlComponents['path']) ? $urlComponents['path'] : '',
            isset($urlComponents['query']) ? '?' . $urlComponents['query'] : '',
            isset($urlComponents['fragment']) ? '#' . $urlComponents['fragment'] : ''
        );
    }

    /**
     * Appends the query parameters depending on domain's formatters defined in configuration.
     *
     * @param string $url
     * @param string $sponsor (i.e. Awe, Cambuilder, Chaturbate)
     *
     * @return string
     */
    public function append($url, $sponsor)
    {
        $urlComponents = parse_url($url);

        $parameters = array();
        if (isset($urlComponents['query'])) {
            parse_str($urlComponents['query'], $parameters);
        }

        $trackingParameters = $this->persister->getTrackingParameters();

        /** Call formatters to get parameters to add to the query's parameters. */
        foreach ($this->formatters as $formatter) {
            /* Add formatter's result to the set of parameters */
            $parameters = array_merge(
                $parameters,
                $formatter->format($trackingParameters)
            );
        }

        /** Search for tracking parameters to replace in query's parameters. */
        foreach ($parameters as $parameterName => $parameterValue) {
            if (preg_match('`^{\s?(?<parameter>[a-z0-9]+)\s?}$`i', $parameterValue, $matches)) {
                $parameters[$parameterName] = $trackingParameters->get($matches['parameter'], null);
            }
        }

        /** Rebuild the query parameters string. */
        $urlComponents['query'] = http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);
        if (true === empty($urlComponents['query'])) {
            /* Force to null to avoid single "?" at the end of url */
            $urlComponents['query'] = null;
        }

        return $this->buildUrl($urlComponents);
    }
}
