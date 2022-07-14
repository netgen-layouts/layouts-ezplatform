<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\Layouts\Ez\Validator\Constraint\Content;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function count;
use function in_array;
use function is_scalar;

/**
 * Validates if the provided value is an ID of a valid content in eZ Platform.
 */
final class ContentValidator extends ConstraintValidator
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof Content) {
            throw new UnexpectedTypeException($constraint, Content::class);
        }

        if (!is_scalar($value)) {
            throw new UnexpectedTypeException($value, 'scalar');
        }

        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
            $contentInfo = $this->repository->sudo(
                static fn (Repository $repository): ContentInfo => $repository->getContentService()->loadContentInfo((int) $value),
            );

            if (count($constraint->allowedTypes) > 0) {
                $contentType = $this->repository->getContentTypeService()->loadContentType(
                    $contentInfo->contentTypeId,
                );

                if (!in_array($contentType->identifier, $constraint->allowedTypes, true)) {
                    $this->context->buildViolation($constraint->typeNotAllowedMessage)
                        ->setParameter('%contentType%', $contentType->identifier)
                        ->addViolation();
                }
            }
        } catch (NotFoundException $e) {
            if (!$constraint->allowInvalid) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%contentId%', (string) $value)
                    ->addViolation();
            }
        }
    }
}
