<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Item\ValueUrlGenerator;

use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\Layouts\Item\ValueUrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ContentValueUrlGenerator implements ValueUrlGeneratorInterface
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
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $object
     */
    public function generate(object $object): ?string
    {
        return $this->urlGenerator->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            [
                'contentId' => $object->id,
            ]
        );
    }
}
