<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;

class Subtree implements TargetHandler
{
    /**
     * Handles the query by adding the clause that matches the provided target values.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param mixed $value
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function handleQuery(QueryBuilder $query, $value)
    {
        $query->andWhere(
            $query->expr()->in('rt.value', array(':target_value'))
        )
        ->setParameter('target_value', $value, Connection::PARAM_INT_ARRAY);
    }
}