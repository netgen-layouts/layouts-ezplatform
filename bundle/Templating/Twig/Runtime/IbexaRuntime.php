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
     * @param int|string $locationId
     *
     * @return string[]
     */
    public function getLocationPath($locationId): array
    {
        try {
            $location = $this->loadLocation((int) $locationId);

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
