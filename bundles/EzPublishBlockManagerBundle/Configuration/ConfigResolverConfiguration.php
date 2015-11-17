<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Configuration;

use Netgen\BlockManager\Configuration\Configuration;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use InvalidArgumentException;

class ConfigResolverConfiguration extends Configuration
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

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
        return $this->configResolver->hasParameter(
            $parameterName,
            ConfigurationInterface::PARAMETER_NAMESPACE
        );
    }

    /**
     * Returns the parameter from configuration.
     *
     * @param string $parameterName
     *
     * @throws \InvalidArgumentException If parameter is undefined
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        if (!$this->hasParameter($parameterName)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "%s" does not exist in configuration.',
                    $parameterName
                )
            );
        }

        return $this->configResolver->getParameter(
            $parameterName,
            ConfigurationInterface::PARAMETER_NAMESPACE
        );
    }
}
