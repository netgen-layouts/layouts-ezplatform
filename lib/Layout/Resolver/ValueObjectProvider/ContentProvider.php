<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Layout\Resolver\ValueObjectProvider;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Netgen\Layouts\Layout\Resolver\ValueObjectProviderInterface;

final class ContentProvider implements ValueObjectProviderInterface
{
    public function __construct(private Repository $repository)
    {
    }

    public function getValueObject(mixed $value): ?object
    {
        try {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
            $content = $this->repository->sudo(
                fn (): Content => $this->repository->getContentService()->loadContent((int) $value),
            );

            return $content->contentInfo->mainLocationId !== null ? $content : null;
        } catch (NotFoundException) {
            return null;
        }
    }
}
