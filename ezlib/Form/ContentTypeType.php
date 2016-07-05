<?php

namespace Netgen\BlockManager\Ez\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use eZ\Publish\API\Repository\ContentTypeService;

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
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('choices', $this->getContentTypes());
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
     * Returns all content types from eZ Publish.
     *
     * @return array
     */
    protected function getContentTypes()
    {
        $allContentTypes = array();

        $groups = $this->contentTypeService->loadContentTypeGroups();
        foreach ($groups as $group) {
            $contentTypes = $this->contentTypeService->loadContentTypes($group);
            foreach ($contentTypes as $contentType) {
                $contentTypeNames = array_values($contentType->getNames());
                $allContentTypes[$contentTypeNames[0]] = $contentType->identifier;
            }
        }

        return $allContentTypes;
    }
}
