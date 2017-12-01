<?php

namespace Netgen\BlockManager\Ez\Validator;

use eZ\Publish\API\Repository\Repository;
use Netgen\BlockManager\Ez\Validator\Constraint\ObjectState;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates if the provided value is an identifier of a valid object state in eZ Platform.
 */
final class ObjectStateValidator extends ConstraintValidator
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * @var array
     */
    private $stateIdentifiers;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof ObjectState) {
            throw new UnexpectedTypeException($constraint, ObjectState::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = explode('|', $value);
        if (!is_array($value) || count($value) !== 2) {
            throw new UnexpectedTypeException($value, 'string with "|" delimiter');
        }

        $stateIdentifiers = $this->loadStateIdentifiers();

        if (!array_key_exists($value[0], $stateIdentifiers)) {
            $this->context->buildViolation($constraint->invalidGroupMessage)
                ->setParameter('%identifier%', $value[0])
                ->addViolation();

            return;
        }

        if (!in_array($value[1], $stateIdentifiers[$value[0]], true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%identifier%', $value[0])
                ->addViolation();

            return;
        }

        if (empty($constraint->allowedStates)) {
            return;
        }

        foreach (array_keys($stateIdentifiers) as $groupIdentifier) {
            if (
                !array_key_exists($groupIdentifier, $constraint->allowedStates) ||
                $constraint->allowedStates[$groupIdentifier] === true
            ) {
                return;
            }

            if (
                is_array($constraint->allowedStates[$groupIdentifier]) &&
                in_array($value[1], $constraint->allowedStates[$groupIdentifier], true)
            ) {
                return;
            }
        }

        $this->context->buildViolation($constraint->notAllowedMessage)
            ->setParameter('%identifier%', $value[1])
            ->setParameter('%groupIdentifier%', $value[0])
            ->addViolation();
    }

    /**
     * Returns the list of object state identifiers separated by group.
     *
     * @return string[][]
     */
    private function loadStateIdentifiers()
    {
        if ($this->stateIdentifiers !== null) {
            return $this->stateIdentifiers;
        }

        return $this->stateIdentifiers = $this->repository->sudo(
            function (Repository $repository) {
                $stateIdentifiers = array();

                $stateGroups = $repository->getObjectStateService()->loadObjectStateGroups();
                foreach ($stateGroups as $stateGroup) {
                    $stateIdentifiers[$stateGroup->identifier] = array();

                    $states = $repository->getObjectStateService()->loadObjectStates($stateGroup);
                    foreach ($states as $state) {
                        $stateIdentifiers[$stateGroup->identifier][] = $state->identifier;
                    }
                }

                return $stateIdentifiers;
            }
        );
    }
}
