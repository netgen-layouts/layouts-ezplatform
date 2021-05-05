<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Configuration;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Exception\ConfigurationException;

/**
 * Implementation of ConfigurationInterface that uses eZ Platform
 * config resolver to retrieve parameters from the container.
 *
 * This means that the returned values will be the ones defined
 * in the current eZ Platform scope of the request.
 *
 * @final
 */
class ConfigResolverConfiguration implements ConfigurationInterface
{
    private ConfigResolverInterface $configResolver;

    private ConfigurationInterface $fallbackConfiguration;

    public function __construct(
        ConfigResolverInterface $configResolver,
        ConfigurationInterface $fallbackConfiguration
    ) {
        $this->configResolver = $configResolver;
        $this->fallbackConfiguration = $fallbackConfiguration;
    }

    public function hasParameter(string $parameterName): bool
    {
        $hasParam = $this->configResolver->hasParameter(
            $parameterName,
            ConfigurationInterface::PARAMETER_NAMESPACE,
        );

        if (!$hasParam) {
            $hasParam = $this->fallbackConfiguration->hasParameter($parameterName);
        }

        return $hasParam;
    }

    public function getParameter(string $parameterName)
    {
        if (!$this->hasParameter($parameterName)) {
            throw ConfigurationException::noParameter($parameterName);
        }

        if (
            $this->configResolver->hasParameter(
                $parameterName,
                ConfigurationInterface::PARAMETER_NAMESPACE,
            )
        ) {
            return $this->configResolver->getParameter(
                $parameterName,
                ConfigurationInterface::PARAMETER_NAMESPACE,
            );
        }

        return $this->fallbackConfiguration->getParameter($parameterName);
    }
}
