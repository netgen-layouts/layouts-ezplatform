<?php

namespace Netgen\BlockManager\Ez\Item\ValueConverter;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\BlockManager\Item\ValueConverterInterface;

class LocationValueConverter implements ValueConverterInterface
{
    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    protected $translationHelper;

    public function __construct(TranslationHelper $translationHelper)
    {
        $this->translationHelper = $translationHelper;
    }

    public function supports($object)
    {
        return $object instanceof Location;
    }

    public function getValueType($object)
    {
        return 'ezlocation';
    }

    public function getId($object)
    {
        return $object->id;
    }

    public function getName($object)
    {
        return $this->translationHelper->getTranslatedContentNameByContentInfo(
            $object->getContentInfo()
        );
    }

    public function getIsVisible($object)
    {
        return !$object->invisible;
    }
}
