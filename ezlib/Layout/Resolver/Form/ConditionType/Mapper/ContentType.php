<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContentType extends Mapper
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
     * Returns the form type that will be used to edit the value of this condition type.
     *
     * @return string
     */
    public function getFormType()
    {
        return ChoiceType::class;
    }

    /**
     * Returns the form type options.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface $conditionType
     *
     * @return array
     */
    public function getOptions(ConditionTypeInterface $conditionType)
    {
        $baseOptions = parent::getOptions($conditionType);

        return array(
            'choices' => $this->getContentTypes(),
            'choice_translation_domain' => false,
            'choices_as_values' => true,
            'multiple' => true,
        ) + $baseOptions;
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
