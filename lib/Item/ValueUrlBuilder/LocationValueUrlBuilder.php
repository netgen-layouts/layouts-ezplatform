<?php

namespace Netgen\BlockManager\Ez\Item\ValueUrlBuilder;

use Netgen\BlockManager\Item\ValueUrlBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

final class LocationValueUrlBuilder implements ValueUrlBuilderInterface
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
        return $this->router->generate($object);
    }
}
