<?php

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\SPI\Persistence\Content\ObjectState\Handler;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Ez\Parameters\ParameterType as EzParameterType;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

trait ObjectStateFilterTrait
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Content\ObjectState\Handler
     */
    private $objectStateHandler;

    /**
     * Sets the objectState handler used by the trait.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\ObjectState\Handler $handler
     */
    private function setObjectStateHandler(Handler $handler)
    {
        $this->objectStateHandler = $handler;
    }

    /**
     * Builds the parameters for filtering by object states.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     * @param array $groups
     */
    private function buildObjectStateFilterParameters(ParameterBuilderInterface $builder, $groups = [])
    {
        $builder->add(
            'filter_by_object_state',
            ParameterType\Compound\BooleanType::class,
            [
                'groups' => $groups,
            ]
        );

        $builder->get('filter_by_object_state')->add(
            'object_states',
            EzParameterType\ObjectStateType::class,
            [
                'multiple' => true,
                'groups' => $groups,
            ]
        );
    }

    /**
     * Returns the criteria used to filter content by object state.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion|null
     */
    private function getObjectStateFilterCriteria(Query $query)
    {
        if ($query->getParameter('filter_by_object_state')->getValue() !== true) {
            return;
        }

        $objectStates = $query->getParameter('object_states')->getValue();
        if (empty($objectStates)) {
            return;
        }

        $criteria = [];
        foreach ($this->getObjectStateIds($objectStates) as $groupId => $stateIds) {
            $criteria[] = new Criterion\ObjectStateId($stateIds);
        }

        return new Criterion\LogicalAnd($criteria);
    }

    /**
     * Returns object state IDs for all provided object state identifiers.
     *
     * State identifiers are in format "<group_identifier>|<state_identifier>"
     *
     * @param array $stateIdentifiers
     *
     * @return array
     */
    private function getObjectStateIds(array $stateIdentifiers)
    {
        $idList = [];

        foreach ($stateIdentifiers as $identifier) {
            $identifier = explode('|', $identifier);
            if (!is_array($identifier) || count($identifier) !== 2) {
                continue;
            }

            try {
                $stateGroup = $this->objectStateHandler->loadGroupByIdentifier($identifier[0]);
                $objectState = $this->objectStateHandler->loadByIdentifier($identifier[1], $stateGroup->id);
                $idList[$stateGroup->id][] = $objectState->id;
            } catch (NotFoundException $e) {
                continue;
            }
        }

        return $idList;
    }
}
