<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Item\ValueConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Repository\Values\MultiLanguageNameTrait;
use Netgen\BlockManager\Item\ValueConverterInterface;

final class LocationValueConverter implements ValueConverterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    private $translationHelper;

    public function __construct(ContentService $contentService, TranslationHelper $translationHelper)
    {
        $this->contentService = $contentService;
        $this->translationHelper = $translationHelper;
    }

    public function supports($object): bool
    {
        return $object instanceof Location;
    }

    public function getValueType($object): string
    {
        return 'ezlocation';
    }

    public function getId($object)
    {
        return $object->id;
    }

    public function getRemoteId($object)
    {
        return $object->remoteId;
    }

    public function getName($object): string
    {
        if (trait_exists(MultiLanguageNameTrait::class)) {
            $versionInfo = $this->contentService->loadVersionInfo($object->getContentInfo());

            return $versionInfo->getName() ?? '';
        }

        // @deprecated BC layer for eZ Publish 5 to fetch content name.
        // Remove when support for eZ Publish 5 ends.

        return $this->translationHelper->getTranslatedContentNameByContentInfo(
            $object->getContentInfo()
        );
    }

    public function getIsVisible($object): bool
    {
        return !$object->invisible;
    }

    public function getObject($object)
    {
        return $object;
    }
}
