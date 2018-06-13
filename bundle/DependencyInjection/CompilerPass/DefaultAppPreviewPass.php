<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DefaultAppPreviewPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('ezpublish.siteaccess.list')) {
            return;
        }

        $defaultRule = [
            'template' => $container->getParameter(
                'netgen_block_manager.app.ezpublish.item_preview_template'
            ),
            'match' => [],
            'params' => [],
        ];

        $scopes = array_merge(
            ['default'],
            $container->getParameter('ezpublish.siteaccess.list')
        );

        foreach ($scopes as $scope) {
            $scopeParams = [
                "ezsettings.{$scope}.content_view",
                "ezsettings.{$scope}.location_view",
            ];

            foreach ($scopeParams as $scopeParam) {
                if (!$container->hasParameter($scopeParam)) {
                    continue;
                }

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
     * @param array $scopeRules
     * @param array $defaultRule
     *
     * @return array
     */
    private function addDefaultPreviewRule($scopeRules, $defaultRule)
    {
        $scopeRules = is_array($scopeRules) ? $scopeRules : [];

        $blockManagerRules = $scopeRules['ngbm_app_preview'] ?? [];

        $blockManagerRules += [
            '___ngbm_app_preview_default___' => $defaultRule,
        ];

        $scopeRules['ngbm_app_preview'] = $blockManagerRules;

        return $scopeRules;
    }
}
