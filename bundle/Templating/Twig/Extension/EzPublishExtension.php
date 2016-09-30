<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Helper\TranslationHelper;
use Twig_SimpleFunction;
use Twig_Extension;
use Exception;

class EzPublishExtension extends Twig_Extension
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    protected $translationHelper;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(Repository $repository, TranslationHelper $translationHelper)
    {
        $this->repository = $repository;
        $this->translationHelper = $translationHelper;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'ngbm_ezcontent_name',
                array($this, 'getContentName')
            ),
            new Twig_SimpleFunction(
                'ngbm_ezlocation_path',
                array($this, 'getLocationPath')
            ),
            new Twig_SimpleFunction(
                'ngbm_ez_content_type_name',
                array($this, 'getContentTypeName')
            ),
        );
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
            return;
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
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $this->loadLocation($locationId);

            $locationPath = $location->path;
            array_shift($locationPath);

            $translatedNames = array();

            for ($i = 0, $pathLength = count($locationPath); $i < $pathLength; ++$i) {
                $locationInPath = $this->loadLocation($locationPath[$i]);
                $translatedNames[] = $this->translationHelper->getTranslatedContentName(
                    $this->loadContent($locationInPath->contentId)
                );
            }

            return $translatedNames;
        } catch (Exception $e) {
            return array();
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

            return $this->translationHelper->getTranslatedByMethod(
                $contentType,
                'getName'
            );
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * Loads the content for provided content ID.
     *
     * @param int|string $contentId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected function loadContent($contentId)
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
    protected function loadLocation($locationId)
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
    protected function loadContentType($identifier)
    {
        return $this->repository->sudo(
            function (Repository $repository) use ($identifier) {
                return $repository->getContentTypeService()->loadContentTypeByIdentifier($identifier);
            }
        );
    }
}
