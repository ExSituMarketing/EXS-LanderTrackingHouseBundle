<?php

namespace EXS\LanderTrackingHouseBundle\Service;

use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterFormatterInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\ParameterBag;

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
     * @var ParameterBag
     */
    private $allTrackingParameters;

    /**
     * TrackingParameterAppender constructor.
     *
     * @param TrackingParameterPersister $persister
     */
    public function __construct(TrackingParameterPersister $persister)
    {
        $this->persister = $persister;
        $this->formatters = [];
        $this->allTrackingParameters = new ParameterBag();
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

        $trackingParameters = $this->persister->getAllTrackingParameters();

        if (null !== $formatterName) {
            $foundFormatter = $this->findFormatterByName($formatterName);

            if (null === $foundFormatter) {
                throw new InvalidConfigurationException(sprintf('Unknown formatter "%s".', $formatterName));
            }

            $parameters = array_merge(
                $parameters,
                $this->formatters[$foundFormatter]->format($trackingParameters)
            );
        }

        /** Search for tracking parameters to replace in query's parameters. */
        foreach ($parameters as $parameterName => $parameterValue) {
            if (preg_match('`^{\s?(?<parameter>[a-z0-9_]+)\s?}$`i', $parameterValue, $matches)) {
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

    /**
     * @param string $parameterName
     *
     * @return string|null
     */
    public function getTrackingParameter($parameterName)
    {
        $trackingParameters = $this->persister->getAllTrackingParameters();

        if (null !== $parameterValue = $trackingParameters->get($parameterName)) {
            return $parameterValue;
        }

        if (0 === $this->allTrackingParameters->count()) {
            $this->allTrackingParameters = clone $trackingParameters;

            foreach ($this->formatters as $formatter) {
                $newParameters = $formatter->format($trackingParameters);

                foreach ($newParameters as $newParameterName => $newParameterValue) {
                    $this->allTrackingParameters->set($newParameterName, $newParameterValue);
                }
            }
        }

        if (null !== $parameterValue = $this->allTrackingParameters->get($parameterName)) {
            return $parameterValue;
        }

        return null;
    }

    /**
     * @param string $formatterName
     *
     * @return mixed|null
     */
    private function findFormatterByName($formatterName)
    {
        $foundFormatters = array_filter(array_keys($this->formatters), function ($formatterIdentifier) use ($formatterName) {
            $pattern = sprintf('`^(?:(?:.*)\.)?%s(?:_(?:.*))?$`i', $formatterName);

            return (0 !== (int)preg_match($pattern, $formatterIdentifier));
        });

        if (empty($foundFormatters)) {
            return null;
        }

        return current($foundFormatters);
    }
}
