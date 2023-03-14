<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\ValueObjectProvider;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\Layouts\Layout\Resolver\ValueObjectProviderInterface;

final class ContentProvider implements ValueObjectProviderInterface
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getValueObject($value): ?Content
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
            $content = $this->repository->sudo(
                static fn (Repository $repository): Content => $repository->getContentService()->loadContent((int) $value),
            );

            return $content->contentInfo->mainLocationId !== null ? $content : null;
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
