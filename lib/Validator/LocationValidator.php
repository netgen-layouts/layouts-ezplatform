<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Validator;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Location as EzLocation;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\BlockManager\Ez\Validator\Constraint\Location;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided value is an ID of a valid location in eZ Platform.
 */
final class LocationValidator extends ConstraintValidator
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof Location) {
            throw new UnexpectedTypeException($constraint, Location::class);
        }

        if (!is_scalar($value)) {
            throw new UnexpectedTypeException($value, 'scalar');
        }

        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $this->repository->sudo(
                static function (Repository $repository) use ($value): EzLocation {
                    return $repository->getLocationService()->loadLocation($value);
                }
            );

            if (count($constraint->allowedTypes ?? []) > 0) {
                $contentType = $this->getContentType($location);

                if (!in_array($contentType->identifier, $constraint->allowedTypes, true)) {
                    $this->context->buildViolation($constraint->typeNotAllowedMessage)
                        ->setParameter('%contentType%', $contentType->identifier)
                        ->addViolation();
                }
            }
        } catch (NotFoundException $e) {
            if (!$constraint->allowInvalid) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%locationId%', (string) $value)
                    ->addViolation();
            }
        }
    }

    /**
     * Loads the content type for provided location.
     *
     * @deprecated Acts as a BC layer for eZ kernel <7.4
     */
    private function getContentType(EzLocation $location): ContentType
    {
        if (method_exists($location, 'getContent') && method_exists($location->getContent(), 'getContentType')) {
            return $location->getContent()->getContentType();
        }

        // @deprecated Remove when support for eZ kernel < 7.4 ends

        return $this->repository->getContentTypeService()->loadContentType(
            $location->contentInfo->contentTypeId
        );
    }
}
