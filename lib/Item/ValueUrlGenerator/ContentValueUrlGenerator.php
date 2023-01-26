<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Item\ValueUrlGenerator;

use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\Layouts\Item\ExtendedValueUrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @implements \Netgen\Layouts\Item\ExtendedValueUrlGeneratorInterface<\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo>
 */
final class ContentValueUrlGenerator implements ExtendedValueUrlGeneratorInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function generateDefaultUrl(object $object): ?string
    {
        return $this->urlGenerator->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            [
                'contentId' => $object->id,
            ],
        );
    }

    public function generateAdminUrl(object $object): ?string
    {
        return $this->urlGenerator->generate(
            'ibexa.content.view',
            [
                'contentId' => $object->id,
            ],
        );
    }

    public function generate(object $object): ?string
    {
        return $this->generateDefaultUrl($object);
    }
}
