<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function is_array;

final class DefaultAppPreviewPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('ezpublish.siteaccess.list')) {
            return;
        }

        $defaultRule = [
            'template' => $container->getParameter(
                'netgen_layouts.app.ezplatform.item_preview_template',
            ),
            'match' => [],
            'params' => [],
        ];

        /** @var array<int, string> $siteAccessList */
        $siteAccessList = $container->getParameter('ezpublish.siteaccess.list');
        $scopes = [...['default'], ...$siteAccessList];

        foreach ($scopes as $scope) {
            $scopeParams = [
                "ezsettings.{$scope}.content_view",
                "ezsettings.{$scope}.location_view",
            ];

            foreach ($scopeParams as $scopeParam) {
                if (!$container->hasParameter($scopeParam)) {
                    continue;
                }

                /** @var array<string, mixed[]>|null $scopeRules */
                $scopeRules = $container->getParameter($scopeParam);
                $scopeRules = $this->addDefaultPreviewRule($scopeRules, $defaultRule);
                $container->setParameter($scopeParam, $scopeRules);
            }
        }
    }

    /**
     * Adds the default eZ content preview template to default scope as a fallback
     * when no preview rules are defined.
     *
     * @param array<string, mixed[]>|null $scopeRules
     * @param array<string, mixed> $defaultRule
     *
     * @return array<string, mixed[]>
     */
    private function addDefaultPreviewRule(?array $scopeRules, array $defaultRule): array
    {
        $scopeRules = is_array($scopeRules) ? $scopeRules : [];

        $layoutsRules = $scopeRules['nglayouts_app_preview'] ?? [];

        $layoutsRules += [
            '___nglayouts_app_preview_default___' => $defaultRule,
        ];

        $scopeRules['nglayouts_app_preview'] = $layoutsRules;

        return $scopeRules;
    }
}
