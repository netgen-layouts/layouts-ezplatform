<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Value\ValueConverter;

use Netgen\BlockManager\Value\ValueConverterInterface;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Helper\TranslationHelper;

class EzContentValueConverter implements ValueConverterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    protected $locationService;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    protected $translationHelper;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(LocationService $locationService, TranslationHelper $translationHelper)
    {
        $this->locationService = $locationService;
        $this->translationHelper = $translationHelper;
    }

    /**
     * Returns if the converter supports the object.
     *
     * @param mixed $object
     *
     * @return bool
     */
    public function supports($object)
    {
        return $object instanceof ContentInfo;
    }

    /**
     * Returns the value type for this object.
     *
     * @param mixed $object
     *
     * @return string
     */
    public function getValueType($object)
    {
        return 'ezcontent';
    }

    /**
     * Returns the object ID.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $object
     *
     * @return int|string
     */
    public function getId($object)
    {
        return $object->id;
    }

    /**
     * Returns the object name.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $object
     *
     * @return string
     */
    public function getName($object)
    {
        return $this->translationHelper->getTranslatedContentNameByContentInfo($object);
    }

    /**
     * Returns if the object is visible.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $object
     *
     * @return bool
     */
    public function getIsVisible($object)
    {
        $mainLocation = $this->locationService->loadLocation($object->mainLocationId);

        return !$mainLocation->invisible;
    }
}
