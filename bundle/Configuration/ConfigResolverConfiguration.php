<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Configuration;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException;

class ConfigResolverConfiguration implements ConfigurationInterface
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface
     */
    protected $fallbackConfiguration;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface $fallbackConfiguration
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        ConfigurationInterface $fallbackConfiguration
    ) {
        $this->configResolver = $configResolver;
        $this->fallbackConfiguration = $fallbackConfiguration;
    }

    /**
     * Returns if parameter exists in configuration.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        $hasParam = $this->configResolver->hasParameter(
            $parameterName,
            ConfigurationInterface::PARAMETER_NAMESPACE
        );

        if (!$hasParam) {
            $hasParam = $this->fallbackConfiguration->hasParameter($parameterName);
        }

        return $hasParam;
    }

    /**
     * Returns the parameter from configuration.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        if (!$this->hasParameter($parameterName)) {
            throw ConfigurationException::noParameter($parameterName);
        }

        if (
            $this->configResolver->hasParameter(
                $parameterName,
                ConfigurationInterface::PARAMETER_NAMESPACE
            )
        ) {
            return $this->configResolver->getParameter(
                $parameterName,
                ConfigurationInterface::PARAMETER_NAMESPACE
            );
        }

        return $this->fallbackConfiguration->getParameter($parameterName);
    }
}
