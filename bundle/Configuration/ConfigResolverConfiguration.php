<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Configuration;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;

class ConfigResolverConfiguration implements ConfigurationInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface
     */
    protected $fallbackConfiguration;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface $fallbackConfiguration
     */
    public function __construct(ConfigurationInterface $fallbackConfiguration)
    {
        $this->fallbackConfiguration = $fallbackConfiguration;
    }

    /**
     * Sets the config resolver.
     *
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function setConfigResolver(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        if (!$this->hasParameter($parameterName)) {
            throw new InvalidArgumentException(
                'parameterName',
                sprintf(
                    'Parameter "%s" does not exist in configuration.',
                    $parameterName
                )
            );
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
