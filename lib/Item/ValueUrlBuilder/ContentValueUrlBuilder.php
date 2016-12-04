<?php

namespace Netgen\BlockManager\Ez\Item\ValueUrlBuilder;

use Netgen\BlockManager\Item\ValueUrlBuilderInterface;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Symfony\Component\Routing\RouterInterface;

class ContentValueUrlBuilder implements ValueUrlBuilderInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Returns the value type for which this URL builder builds the URL.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'ezcontent';
    }

    /**
     * Returns the object URL. Take note that this is not a slug,
     * but a full path, i.e. starting with /.
     *
     * @param mixed $object
     *
     * @return string
     */
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
