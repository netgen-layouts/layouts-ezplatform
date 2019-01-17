<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Item\ValueUrlGenerator;

use Netgen\BlockManager\Item\ValueUrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocationValueUrlGenerator implements ValueUrlGeneratorInterface
{
    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $object
     */
    public function generate($object): ?string
    {
        return $this->urlGenerator->generate($object);
    }
}
