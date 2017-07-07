<?php

namespace Netgen\BlockManager\Ez\Form;

use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeType extends AbstractType
{
    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('types', array());
        $resolver->setRequired(array('types'));
        $resolver->setAllowedTypes('types', 'array');

        $resolver->setDefault(
            'choices',
            function (Options $options) {
                return $this->getContentTypes($options);
            }
        );

        $resolver->setDefault('choices_as_values', true);
        $resolver->setDefault('choice_translation_domain', false);
    }

    /**
     * Returns the name of the parent type.
     */
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
    protected function getContentTypes(Options $options)
    {
        $allContentTypes = array();

        $groups = $this->contentTypeService->loadContentTypeGroups();
        $configuredGroups = $options['types'];

        foreach ($groups as $group) {
            $configuredGroups += array($group->identifier => true);
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
