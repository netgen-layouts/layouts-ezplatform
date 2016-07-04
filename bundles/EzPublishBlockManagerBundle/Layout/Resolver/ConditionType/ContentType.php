<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType;

use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\ContentService;

class ContentType implements ConditionTypeInterface
{
    use RequestStackAwareTrait;

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
     * Returns if this condition matches the provided value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matches($value)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return false;
        }

        if (!$currentRequest->attributes->has('contentId')) {
            return false;
        }

        if (!is_array($value) || empty($value)) {
            return false;
        }

        $contentInfo = $this->contentService->loadContentInfo(
            $currentRequest->attributes->get('contentId')
        );

        $contentType = $this->contentTypeService->loadContentType(
            $contentInfo->contentTypeId
        );

        return in_array($contentType->identifier, $value);
    }
}
