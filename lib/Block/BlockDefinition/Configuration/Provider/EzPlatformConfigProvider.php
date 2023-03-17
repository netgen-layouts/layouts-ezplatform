<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Block\BlockDefinition\Configuration\Provider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\View\ViewManagerInterface;
use EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ConfigProviderInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;

use function array_combine;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function in_array;
use function is_array;
use function mb_strtolower;
use function preg_replace;
use function sort;
use function trim;
use function ucwords;

final class EzPlatformConfigProvider implements ConfigProviderInterface
{
    private ConfigResolverInterface $configResolver;

    /**
     * @var array<string, string[]>
     */
    private array $groupsBySiteAccess;

    private string $parameterName;

    private string $configResolverParameterName;

    /**
     * @var array<string, \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType[]>
     */
    private array $viewTypes;

    /**
     * @param array<string, string[]> $groupsBySiteAccess
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        array $groupsBySiteAccess,
        string $parameterName,
        string $configResolverParameterName
    ) {
        $this->configResolver = $configResolver;
        $this->groupsBySiteAccess = $groupsBySiteAccess;
        $this->parameterName = $parameterName;
        $this->configResolverParameterName = $configResolverParameterName;
    }

    public function provideViewTypes(?Block $block = null): array
    {
        if ($block === null) {
            return [];
        }

        $validViews = [];
        $validParameters = [];
        $contentTypeIdentifier = $block->getParameter($this->parameterName)->getValue();

        foreach ($this->groupsBySiteAccess as $siteAccess => $groups) {
            if (in_array(EzPlatformAdminUiBundle::ADMIN_GROUP_NAME, $groups, true)) {
                continue;
            }

            /** @var array<string, mixed[]> $contentView */
            $contentView = $this->configResolver->getParameter($this->configResolverParameterName, null, $siteAccess);

            foreach ($contentView as $view => $viewConfigList) {
                if ($view === ViewManagerInterface::VIEW_TYPE_FULL) {
                    continue;
                }

                foreach ($viewConfigList as $viewConfig) {
                    $contentTypeMatch = (array) ($viewConfig['match']['Identifier\ContentType'] ?? []);

                    if (in_array($contentTypeIdentifier, $contentTypeMatch, true)) {
                        $validViews[$view] = $view;
                        $validParameters[$view] ??= null;

                        /** @var array<string>|null $viewConfigValidParameters */
                        $viewConfigValidParameters = $viewConfig['params']['valid_parameters'] ?? null;

                        if (is_array($viewConfigValidParameters)) {
                            $validParameters[$view] = is_array($validParameters[$view]) ?
                                array_merge($validParameters[$view], $viewConfigValidParameters) :
                                $viewConfigValidParameters;
                        }
                    }
                }
            }
        }

        sort($validViews);

        $this->viewTypes[$block->getId()->toString()] ??= $this->buildViewTypes($validViews, $validParameters);

        return $this->viewTypes[$block->getId()->toString()];
    }

    /**
     * Builds the view type objects from the provided configuration.
     *
     * @param string[] $validViews
     * @param array<string, string[]|null> $validParameters
     *
     * @return \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType[]
     */
    private function buildViewTypes(array $validViews, array $validParameters): array
    {
        return array_combine(
            $validViews,
            array_map(
                fn (string $view) => ViewType::fromArray(
                    [
                        'identifier' => $view,
                        'name' => $this->humanize($view),
                        'itemViewTypes' => [
                            'standard' => ItemViewType::fromArray(
                                [
                                    'identifier' => 'standard',
                                    'name' => 'Standard',
                                ],
                            ),
                        ],
                        'validParameters' => is_array($validParameters[$view]) ?
                            array_values(array_unique($validParameters[$view])) :
                            null,
                    ],
                ),
                $validViews,
            ),
        );
    }

    /**
     * Returns the human readable version of the provided string.
     */
    private function humanize(string $text): string
    {
        return ucwords(mb_strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $text) ?? '')));
    }
}
