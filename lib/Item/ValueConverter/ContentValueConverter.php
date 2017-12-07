<?php

namespace Netgen\BlockManager\Ez\Item\ValueConverter;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\BlockManager\Item\ValueConverterInterface;

final class ContentValueConverter implements ValueConverterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    private $translationHelper;

    public function __construct(LocationService $locationService, TranslationHelper $translationHelper)
    {
        $this->locationService = $locationService;
        $this->translationHelper = $translationHelper;
    }

    public function supports($object)
    {
        return $object instanceof ContentInfo;
    }

    public function getValueType($object)
    {
        return 'ezcontent';
    }

    public function getId($object)
    {
        return $object->id;
    }

    public function getRemoteId($object)
    {
        return $object->remoteId;
    }

    public function getName($object)
    {
        return $this->translationHelper->getTranslatedContentNameByContentInfo($object);
    }

    public function getIsVisible($object)
    {
        $mainLocation = $this->locationService->loadLocation($object->mainLocationId);

        return !$mainLocation->invisible;
    }

    public function getObject($object)
    {
        return $object;
    }
}
