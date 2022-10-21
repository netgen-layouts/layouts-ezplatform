<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\ValueObjectProvider;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;

final class ContentProvider implements ValueObjectProviderInterface
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getValueObject($value): ?object
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
            $content = $this->repository->sudo(
                static fn (Repository $repository): Content => $repository->getContentService()->loadContent((string) $value),
            );

            return $content;
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
