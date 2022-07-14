<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Validator;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ContentType\ContentType as APIContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use Netgen\Layouts\Ez\Validator\Constraint\ContentType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function array_key_exists;
use function array_map;
use function count;
use function in_array;
use function is_array;
use function is_string;

/**
 * Validates if the provided value is an identifier of a valid content type in eZ Platform.
 */
final class ContentTypeValidator extends ConstraintValidator
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

        if (!$constraint instanceof ContentType) {
            throw new UnexpectedTypeException($constraint, ContentType::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        try {
            /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
            $contentType = $this->repository->sudo(
                static fn (Repository $repository): APIContentType => $repository->getContentTypeService()->loadContentTypeByIdentifier($value),
            );
        } catch (NotFoundException $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%identifier%', $value)
                ->addViolation();

            return;
        }

        $groupIdentifiers = array_map(
            static fn (ContentTypeGroup $group): string => $group->identifier,
            $contentType->getContentTypeGroups(),
        );

        if (count($constraint->allowedTypes) === 0) {
            return;
        }

        foreach ($groupIdentifiers as $groupIdentifier) {
            if (
                !array_key_exists($groupIdentifier, $constraint->allowedTypes)
                || $constraint->allowedTypes[$groupIdentifier] === true
            ) {
                return;
            }

            if (
                is_array($constraint->allowedTypes[$groupIdentifier])
                && in_array($contentType->identifier, $constraint->allowedTypes[$groupIdentifier], true)
            ) {
                return;
            }
        }

        $this->context->buildViolation($constraint->notAllowedMessage)
            ->setParameter('%identifier%', $value)
            ->addViolation();
    }
}
