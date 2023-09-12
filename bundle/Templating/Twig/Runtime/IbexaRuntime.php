<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Throwable;

use function array_shift;

final class IbexaRuntime
{
    public function __construct(private Repository $repository) {}

    /**
     * Returns the content name.
     */
    public function getContentName(int|string $contentId): string
    {
        try {
            $content = $this->loadContent((int) $contentId);

            return $content->getName() ?? '';
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Returns the location path.
     *
     * @return string[]
     */
    public function getLocationPath(int|string|Location $location): array
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
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * Returns the main location path for provided content.
     *
     * @return string[]
     */
    public function getContentPath(int|string|Content $content): array
    {
        try {
            if (!$content instanceof Content) {
                $content = $this->loadContent((int) $content);
            }

            return $this->getLocationPath((int) $content->contentInfo->mainLocationId);
        } catch (Throwable) {
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
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Loads the content for provided content ID.
     */
    private function loadContent(int $contentId): Content
    {
        return $this->repository->sudo(
            fn (): Content => $this->repository->getContentService()->loadContent($contentId),
        );
    }

    /**
     * Loads the location for provided location ID.
     */
    private function loadLocation(int $locationId): Location
    {
        return $this->repository->sudo(
            fn (): Location => $this->repository->getLocationService()->loadLocation($locationId),
        );
    }

    /**
     * Loads the content type for provided identifier.
     */
    private function loadContentType(string $identifier): ContentType
    {
        return $this->repository->sudo(
            fn (): ContentType => $this->repository->getContentTypeService()->loadContentTypeByIdentifier($identifier),
        );
    }
}
