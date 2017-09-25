<?php

namespace Netgen\BlockManager\Ez\Item\ValueUrlBuilder;

use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\BlockManager\Item\ValueUrlBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

final class ContentValueUrlBuilder implements ValueUrlBuilderInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getUrl($object)
    {
        return $this->router->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            array(
                'contentId' => $object->id,
            )
        );
    }
}
