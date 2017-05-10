<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\ConditionType;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class ContentType implements ConditionTypeInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(ContentService $contentService, ContentTypeService $contentTypeService)
    {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Returns the condition type.
     *
     * @return string
     */
    public function getType()
    {
        return 'ez_content_type';
    }

    /**
     * Returns the constraints that will be used to validate the condition value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Type(array('type' => 'array')),
            new Constraints\All(
                array(
                    'constraints' => array(
                        new Constraints\Type(array('type' => 'string')),
                        new EzConstraints\ContentType(),
                    ),
                )
            ),
        );
    }

    /**
     * Returns if this request matches the provided value.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $value
     *
     * @return bool
     */
    public function matches(Request $request, $value)
    {
        if (!$request->attributes->has('contentId')) {
            return false;
        }

        if (!is_array($value) || empty($value)) {
            return false;
        }

        $contentInfo = $this->contentService->loadContentInfo(
            $request->attributes->get('contentId')
        );

        $contentType = $this->contentTypeService->loadContentType(
            $contentInfo->contentTypeId
        );

        return in_array($contentType->identifier, $value, true);
    }
}
