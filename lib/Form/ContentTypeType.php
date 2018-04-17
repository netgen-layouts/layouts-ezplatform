<?php

namespace Netgen\BlockManager\Ez\Form;

use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentTypeType extends AbstractType
{
    use ChoicesAsValuesTrait;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('types', []);
        $resolver->setRequired(['types']);
        $resolver->setAllowedTypes('types', 'array');

        $resolver->setDefault(
            'choices',
            function (Options $options) {
                return $this->getContentTypes($options);
            }
        );

        $resolver->setDefault('choice_translation_domain', false);

        $resolver->setDefaults(
            $this->getChoicesAsValuesOption()
        );
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * Returns the allowed content types from eZ Publish.
     *
     * @param \Symfony\Component\OptionsResolver\Options $options
     *
     * @return array
     */
    private function getContentTypes(Options $options)
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

                $contentTypeNames = array_values($contentType->getNames());
                $allContentTypes[$group->identifier][$contentTypeNames[0]] = $contentType->identifier;
            }
        }

        return $allContentTypes;
    }
}
