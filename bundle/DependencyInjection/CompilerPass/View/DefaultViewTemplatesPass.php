<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\DependencyInjection\CompilerPass\View;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DefaultViewTemplatesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('ezpublish.siteaccess.list')) {
            return;
        }

        $scopes = array_merge(
            ['default'],
            $container->getParameter('ezpublish.siteaccess.list')
        );

        foreach ($scopes as $scope) {
            $scopeParam = "netgen_layouts.{$scope}.view";
            if (!$container->hasParameter($scopeParam)) {
                continue;
            }

            $scopeRules = $container->getParameter($scopeParam);
            $scopeRules = $this->updateRules($container, $scopeRules);
            $container->setParameter($scopeParam, $scopeRules);
        }
    }

    /**
     * Updates all view rules to add the default template match.
     *
     * @param mixed[]|null $allRules
     *
     * @return mixed[]
     */
    private function updateRules(ContainerBuilder $container, ?array $allRules): array
    {
        $allRules = is_array($allRules) ? $allRules : [];

        $defaultTemplates = $container->getParameter('netgen_layouts.default_view_templates');

        foreach ($defaultTemplates as $viewName => $viewTemplates) {
            foreach ($viewTemplates as $context => $template) {
                $rules = [];

                if (isset($allRules[$viewName][$context]) && is_array($allRules[$viewName][$context])) {
                    $rules = $allRules[$viewName][$context];
                }

                $rules = $this->addDefaultRule($viewName, $context, $rules, $template);

                $allRules[$viewName][$context] = $rules;
            }
        }

        return $allRules;
    }

    /**
     * Adds the default view template as a fallback to specified view rules.
     *
     * @param mixed[] $rules
     *
     * @return mixed[]
     */
    private function addDefaultRule(string $viewName, string $context, array $rules, string $defaultTemplate): array
    {
        $rules += [
            "___{$viewName}_{$context}_default___" => [
                'template' => $defaultTemplate,
                'match' => [],
                'parameters' => [],
            ],
        ];

        return $rules;
    }
}
