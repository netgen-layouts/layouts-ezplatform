<?php

namespace Netgen\BlockManager\Ez\Item\ValueUrlBuilder;

use Netgen\BlockManager\Item\ValueUrlBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocationValueUrlBuilder implements ValueUrlBuilderInterface
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
        return $this->urlGenerator->generate($object);
    }
}
