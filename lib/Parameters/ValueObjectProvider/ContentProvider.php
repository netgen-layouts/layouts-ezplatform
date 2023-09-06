<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\ValueObjectProvider;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\Layouts\Error\ErrorHandlerInterface;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;

final class ContentProvider implements ValueObjectProviderInterface
{
    private Repository $repository;

    private ErrorHandlerInterface $errorHandler;

    public function __construct(Repository $repository, ErrorHandlerInterface $errorHandler)
    {
        $this->repository = $repository;
        $this->errorHandler = $errorHandler;
    }

    public function getValueObject($value): ?Content
    {
        if ($value === null) {
            return null;
        }

        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
            $content = $this->repository->sudo(
                static fn (Repository $repository): Content => $repository->getContentService()->loadContent((int) $value),
            );

            return $content->contentInfo->mainLocationId !== null ? $content : null;
        } catch (NotFoundException $e) {
            $this->errorHandler->logError($e);

            return null;
        }
    }
}
