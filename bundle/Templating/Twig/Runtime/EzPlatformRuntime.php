<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Templating\Twig\Runtime;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Throwable;

use function array_shift;

final class EzPlatformRuntime
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Returns the content name.
     *
     * @param int|string $contentId
     */
    public function getContentName($contentId): string
    {
        try {
            $content = $this->loadContent((int) $contentId);

            return $content->getName() ?? '';
        } catch (Throwable $t) {
            return '';
        }
    }

    /**
     * Returns the location path.
     *
     * @param int|string|\eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return string[]
     */
    public function getLocationPath($location): array
    {
        try {
            if (!$location instanceof Location) {
                $location = $this->loadLocation((int) $location);
            }

            $locationPath = $location->path;
            array_shift($locationPath);

            $translatedNames = [];

            foreach ($locationPath as $locationPathId) {
                $locationInPath = $this->loadLocation((int) $locationPathId);
                $translatedNames[] = $this->getContentName($locationInPath->contentInfo->id);
            }

            return $translatedNames;
        } catch (Throwable $t) {
            return [];
        }
    }

    /**
     * Returns the main location path for provided content.
     *
     * @param int|string|\eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return string[]
     */
    public function getContentPath($content): array
    {
        try {
            if (!$content instanceof Content) {
                $content = $this->loadContent((int) $content);
            }

            return $this->getLocationPath((int) $content->contentInfo->mainLocationId);
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

            return $contentType->getName() ?? '';
        } catch (Throwable $t) {
            return '';
        }
    }

    /**
     * Loads the content for provided content ID.
     */
    private function loadContent(int $contentId): Content
    {
        return $this->repository->sudo(
            static fn (Repository $repository): Content => $repository->getContentService()->loadContent($contentId),
        );
    }

    /**
     * Loads the location for provided location ID.
     */
    private function loadLocation(int $locationId): Location
    {
        return $this->repository->sudo(
            static fn (Repository $repository): Location => $repository->getLocationService()->loadLocation($locationId),
        );
    }

    /**
     * Loads the content type for provided identifier.
     */
    private function loadContentType(string $identifier): ContentType
    {
        return $this->repository->sudo(
            static fn (Repository $repository): ContentType => $repository->getContentTypeService()->loadContentTypeByIdentifier($identifier),
        );
    }
}
