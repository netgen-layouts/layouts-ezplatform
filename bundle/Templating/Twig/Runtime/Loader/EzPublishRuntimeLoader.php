<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\Loader;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Helper\TranslationHelper;
use Twig_RuntimeLoaderInterface;

/**
 * Runtime loader for EzPublishRuntime class.
 *
 * @deprecated Remove when support for Symfony 2.8 ends.
 */
class EzPublishRuntimeLoader implements Twig_RuntimeLoaderInterface
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
     * Creates the runtime implementation of a Twig element (filter/function/test).
     *
     * @param string $class A runtime class
     *
     * @return object|null The runtime instance or null if the loader does not know how to create the runtime for this class
     */
    public function load($class)
    {
        if ($class !== EzPublishRuntime::class) {
            return null;
        }

        return new EzPublishRuntime(
            $this->repository,
            $this->translationHelper
        );
    }
}
