<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Collection\QueryType\Handler\Traits;

use Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Netgen\Layouts\Ibexa\Parameters\ParameterType as IbexaParameterType;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterType;
use function count;
use function explode;

trait ObjectStateFilterTrait
{
    private Handler $objectStateHandler;

    /**
     * Sets the objectState handler used by the trait.
     */
    private function setObjectStateHandler(Handler $handler): void
    {
        $this->objectStateHandler = $handler;
    }

    /**
     * Builds the parameters for filtering by object states.
     *
     * @param string[] $groups
     */
    private function buildObjectStateFilterParameters(ParameterBuilderInterface $builder, array $groups = []): void
    {
        $builder->add(
            'filter_by_object_state',
            ParameterType\Compound\BooleanType::class,
            [
                'groups' => $groups,
            ],
        );

        $builder->get('filter_by_object_state')->add(
            'object_states',
            IbexaParameterType\ObjectStateType::class,
            [
                'multiple' => true,
                'groups' => $groups,
            ],
        );
    }

    /**
     * Returns the criteria used to filter content by object state.
     */
    private function getObjectStateFilterCriteria(ParameterCollectionInterface $parameterCollection): ?Criterion
    {
        if ($parameterCollection->getParameter('filter_by_object_state')->getValue() !== true) {
            return null;
        }

        $objectStates = $parameterCollection->getParameter('object_states')->getValue() ?? [];
        if (count($objectStates) === 0) {
            return null;
        }

        $criteria = [];
        foreach ($this->getObjectStateIds($objectStates) as $stateIds) {
            $criteria[] = new Criterion\ObjectStateId($stateIds);
        }

        return new Criterion\LogicalAnd($criteria);
    }

    /**
     * Returns object state IDs for all provided object state identifiers.
     *
     * State identifiers are in format "<group_identifier>|<state_identifier>"
     *
     * @param string[] $stateIdentifiers
     *
     * @return array<int, int[]>
     */
    private function getObjectStateIds(array $stateIdentifiers): array
    {
        $idList = [];

        foreach ($stateIdentifiers as $identifier) {
            $identifier = explode('|', $identifier);
            if (count($identifier) !== 2) {
                continue;
            }

            try {
                $stateGroup = $this->objectStateHandler->loadGroupByIdentifier($identifier[0]);
                $objectState = $this->objectStateHandler->loadByIdentifier($identifier[1], $stateGroup->id);
                $idList[(int) $stateGroup->id][] = (int) $objectState->id;
            } catch (NotFoundException $e) {
                continue;
            }
        }

        return $idList;
    }
}
