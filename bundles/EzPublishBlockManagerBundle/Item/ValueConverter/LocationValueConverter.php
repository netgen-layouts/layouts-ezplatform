<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueConverter;

use Netgen\BlockManager\Item\ValueConverterInterface;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Helper\TranslationHelper;

class LocationValueConverter implements ValueConverterInterface
{
    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    protected $translationHelper;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(TranslationHelper $translationHelper)
    {
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
        return $object instanceof Location;
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
        return 'ezlocation';
    }

    /**
     * Returns the object ID.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
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
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     *
     * @return string
     */
    public function getName($object)
    {
        return $this->translationHelper->getTranslatedContentNameByContentInfo(
            $object->getContentInfo()
        );
    }

    /**
     * Returns if the object is visible.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     *
     * @return bool
     */
    public function getIsVisible($object)
    {
        return !$object->invisible;
    }
}
