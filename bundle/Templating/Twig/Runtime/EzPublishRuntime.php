<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Repository\Values\MultiLanguageNameTrait;
use Throwable;

final class EzPublishRuntime
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    private $translationHelper;

    public function __construct(Repository $repository, TranslationHelper $translationHelper)
    {
        $this->repository = $repository;
        $this->translationHelper = $translationHelper;
    }

    /**
     * Returns the content name.
     *
     * @param int|string $contentId
     *
     * @return string
     */
    public function getContentName($contentId): string
    {
        try {
            $versionInfo = $this->loadVersionInfo($contentId);

            if (trait_exists(MultiLanguageNameTrait::class)) {
                return $versionInfo->getName() ?? '';
            }

            // Below is a @deprecated BC layer for eZ Publish 5 to fetch content name.
            // Remove when support for eZ Publish 5 ends.

            $content = $this->loadContent($contentId);

            return $this->translationHelper->getTranslatedContentName($content);
        } catch (Throwable $t) {
            return '';
        }
    }

    /**
     * Returns the location path.
     *
     * @param int|string $locationId
     *
     * @return string[]
     */
    public function getLocationPath($locationId): array
    {
        try {
            $location = $this->loadLocation($locationId);

            $locationPath = $location->path;
            array_shift($locationPath);

            $translatedNames = [];

            foreach ($locationPath as $locationPathId) {
                $locationInPath = $this->loadLocation($locationPathId);
                $translatedNames[] = $this->getContentName($locationInPath->contentInfo->id);
            }

            return $translatedNames;
        } catch (Throwable $t) {
            return [];
        }
    }

    /**
     * Returns the content type name.
     */
    public function getContentTypeName(string $identifier): string
    {
        try {
            $contentType = $this->loadContentType($identifier);

            if (trait_exists(MultiLanguageNameTrait::class)) {
                return $contentType->getName() ?? '';
            }

            // Below is a @deprecated BC layer for eZ Publish 5 to fetch content type name.
            // Remove when support for eZ Publish 5 ends.

            $contentTypeName = $this->translationHelper->getTranslatedByMethod(
                $contentType,
                'getName'
            );

            if ($contentTypeName !== null) {
                return $contentTypeName;
            }

            $contentTypeNames = $contentType->getNames();
            if (empty($contentTypeNames)) {
                return '';
            }

            return array_values($contentTypeNames)[0];
        } catch (Throwable $t) {
            return '';
        }
    }

    /**
     * Loads the content for provided content ID.
     *
     * @param int|string $contentId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    private function loadContent($contentId): Content
    {
        return $this->repository->sudo(
            function (Repository $repository) use ($contentId): Content {
                return $repository->getContentService()->loadContent($contentId);
            }
        );
    }

    /**
     * Loads the version info for provided content ID.
     *
     * @param int|string $contentId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\VersionInfo
     */
    private function loadVersionInfo($contentId): VersionInfo
    {
        return $this->repository->sudo(
            function (Repository $repository) use ($contentId): VersionInfo {
                return $repository->getContentService()->loadVersionInfoById($contentId);
            }
        );
    }

    /**
     * Loads the location for provided location ID.
     *
     * @param int|string $locationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    private function loadLocation($locationId): Location
    {
        return $this->repository->sudo(
            function (Repository $repository) use ($locationId): Location {
                return $repository->getLocationService()->loadLocation($locationId);
            }
        );
    }

    /**
     * Loads the content type for provided identifier.
     */
    private function loadContentType(string $identifier): ContentType
    {
        return $this->repository->sudo(
            function (Repository $repository) use ($identifier): ContentType {
                return $repository->getContentTypeService()->loadContentTypeByIdentifier($identifier);
            }
        );
    }
}
