<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Doctrine\DBAL\Connection;
use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;
use Doctrine\DBAL\Query\QueryBuilder;

class Subtree extends Location
{
    /**
     * Returns the target identifier this handler handles.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'subtree';
    }

    /**
     * Handles the query by adding the clause that matches the provided values.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param array $values
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function handleQuery(QueryBuilder $query, array $values)
    {
        $query->andWhere(
            $query->expr()->in('rv.value', array(':rule_value'))
        )
        ->setParameter('rule_value', $values, Connection::PARAM_INT_ARRAY);
    }
}
