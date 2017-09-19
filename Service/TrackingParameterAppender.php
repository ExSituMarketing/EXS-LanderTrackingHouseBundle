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
     * @param string $formatterName
     *
     * @return string
     */
    public function append($url, $formatterName = null)
    {
        $urlComponents = parse_url($url);

        $parameters = array();
        if (isset($urlComponents['query'])) {
            parse_str($urlComponents['query'], $parameters);
        }

        $trackingParameters = $this->persister->getTrackingParameters();

        if (null === $formatterName) {
            /** Search for tracking parameters to replace in query's parameters. */
            foreach ($parameters as $parameterName => $parameterValue) {
                if (preg_match('`^{\s?(?<parameter>[a-z0-9_]+)\s?}$`i', $parameterValue, $matches)) {
                    $parameters[$parameterName] = $trackingParameters->get($matches['parameter'], null);
                }
            }
        } else {
            /** Call formatter to get parameters to add to the query's parameters. */
            $foundFilters = array_filter(array_keys($this->formatters), function ($formatterId) use ($formatterName) {
                $pattern = sprintf('`^(?:(?:.*)\.)?%s(?:_(?:.*))?$`i', $formatterName);

                return (0 !== (int)preg_match($pattern, $formatterId));
            });

            if (empty($foundFilters)) {
                throw new InvalidConfigurationException(sprintf('Unknown formatter "%s".', $formatterName));
            }

            $parameters = array_merge(
                $parameters,
                $this->formatters[current($foundFilters)]->format($trackingParameters)
            );
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
