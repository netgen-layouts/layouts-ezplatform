<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime;

use Exception;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Helper\TranslationHelper;

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
    public function getContentName($contentId)
    {
        try {
            $content = $this->loadContent($contentId);

            return $this->translationHelper->getTranslatedContentName($content);
        } catch (Exception $e) {
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
    public function getLocationPath($locationId)
    {
        try {
            $location = $this->loadLocation($locationId);

            $locationPath = $location->path;
            array_shift($locationPath);

            $translatedNames = [];

            for ($i = 0, $pathLength = count($locationPath); $i < $pathLength; ++$i) {
                $locationInPath = $this->loadLocation($locationPath[$i]);
                $translatedNames[] = $this->translationHelper->getTranslatedContentName(
                    $this->loadContent($locationInPath->contentInfo->id)
                );
            }

            return $translatedNames;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Returns the content type name.
     *
     * @param string $identifier
     *
     * @return string
     */
    public function getContentTypeName($identifier)
    {
        try {
            $contentType = $this->loadContentType($identifier);

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
        } catch (Exception $e) {
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
    private function loadContent($contentId)
    {
        return $this->repository->sudo(
            function (Repository $repository) use ($contentId) {
                return $repository->getContentService()->loadContent($contentId);
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
    private function loadLocation($locationId)
    {
        return $this->repository->sudo(
            function (Repository $repository) use ($locationId) {
                return $repository->getLocationService()->loadLocation($locationId);
            }
        );
    }

    /**
     * Loads the content type for provided identifier.
     *
     * @param string $identifier
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    private function loadContentType($identifier)
    {
        return $this->repository->sudo(
            function (Repository $repository) use ($identifier) {
                return $repository->getContentTypeService()->loadContentTypeByIdentifier($identifier);
            }
        );
    }
}
