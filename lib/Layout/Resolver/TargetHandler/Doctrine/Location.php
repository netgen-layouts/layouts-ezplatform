<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\TargetHandler\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Layout\Resolver\TargetHandler\Doctrine\TargetHandlerInterface;

final class Location implements TargetHandlerInterface
{
    public function handleQuery(QueryBuilder $query, $value)
    {
        $query->andWhere(
            $query->expr()->eq('rt.value', ':target_value')
        )
        ->setParameter('target_value', $value, Type::INTEGER);
    }
}
