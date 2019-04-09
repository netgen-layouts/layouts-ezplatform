<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Form;

use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentTypeType extends AbstractType
{
    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('types', []);
        $resolver->setRequired(['types']);
        $resolver->setAllowedTypes('types', 'array');

        $resolver->setDefault(
            'choices',
            function (Options $options): array {
                return $this->getContentTypes($options);
            }
        );

        $resolver->setDefault('choice_translation_domain', false);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * Returns the allowed content types from eZ Platform.
     */
    private function getContentTypes(Options $options): array
    {
        $allContentTypes = [];

        $groups = $this->contentTypeService->loadContentTypeGroups();
        $configuredGroups = $options['types'];

        foreach ($groups as $group) {
            $configuredGroups += [$group->identifier => true];
            if ($configuredGroups[$group->identifier] === false) {
                continue;
            }

            $contentTypes = $this->contentTypeService->loadContentTypes($group);
            foreach ($contentTypes as $contentType) {
                if (
                    is_array($configuredGroups[$group->identifier]) &&
                    !in_array($contentType->identifier, $configuredGroups[$group->identifier], true)
                ) {
                    continue;
                }

                $contentTypeName = $contentType->getName() ?? $contentType->identifier;
                $allContentTypes[$group->identifier][$contentTypeName] = $contentType->identifier;
            }
        }

        return $allContentTypes;
    }
}
