<?php

namespace Netgen\BlockManager\Ez\Item\ValueUrlBuilder;

use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\BlockManager\Item\ValueUrlBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ContentValueUrlBuilder implements ValueUrlBuilderInterface
{
    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getUrl($object)
    {
        return $this->urlGenerator->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            array(
                'contentId' => $object->id,
            )
        );
    }
}
