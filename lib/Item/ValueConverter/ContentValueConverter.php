<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Item\ValueConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Repository\Values\MultiLanguageNameTrait;
use Netgen\BlockManager\Item\ValueConverterInterface;

final class ContentValueConverter implements ValueConverterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    private $translationHelper;

    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        TranslationHelper $translationHelper
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->translationHelper = $translationHelper;
    }

    public function supports($object): bool
    {
        return $object instanceof ContentInfo;
    }

    public function getValueType($object): string
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

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $object
     *
     * @return string
     */
    public function getName($object): string
    {
        if (trait_exists(MultiLanguageNameTrait::class)) {
            $versionInfo = $this->contentService->loadVersionInfo($object);

            return $versionInfo->getName() ?? '';
        }

        // @deprecated BC layer for eZ Publish 5 to fetch content name.
        // Remove when support for eZ Publish 5 ends.

        return $this->translationHelper->getTranslatedContentNameByContentInfo($object);
    }

    public function getIsVisible($object): bool
    {
        $mainLocation = $this->locationService->loadLocation($object->mainLocationId);

        return !$mainLocation->invisible;
    }

    public function getObject($object)
    {
        return $object;
    }
}
