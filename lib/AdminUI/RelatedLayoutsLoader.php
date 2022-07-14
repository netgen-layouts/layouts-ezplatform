<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\AdminUI;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Value;
use Ramsey\Uuid\Uuid;

use function array_map;

final class RelatedLayoutsLoader
{
    private LayoutService $layoutService;

    private Connection $databaseConnection;

    public function __construct(LayoutService $layoutService, Connection $databaseConnection)
    {
        $this->layoutService = $layoutService;
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Returns all layouts related to provided location and its content, sorted by name.
     *
     * Related layout is a layout where the location or its content are referenced by
     * a manual item in one of the block collections.
     *
     * @return \Netgen\Layouts\API\Values\Layout\Layout[]
     */
    public function loadRelatedLayouts(Location $location): array
    {
        $query = $this->databaseConnection->createQueryBuilder();

        $query->select('DISTINCT l.uuid, l.name')
            ->from('nglayouts_collection_item', 'ci')
            ->innerJoin(
                'ci',
                'nglayouts_block_collection',
                'bc',
                $query->expr()->and(
                    $query->expr()->eq('bc.collection_id', 'ci.collection_id'),
                    $query->expr()->eq('bc.collection_status', 'ci.status'),
                ),
            )
            ->innerJoin(
                'bc',
                'nglayouts_block',
                'b',
                $query->expr()->and(
                    $query->expr()->eq('b.id', 'bc.block_id'),
                    $query->expr()->eq('b.status', 'bc.block_status'),
                ),
            )
            ->innerJoin(
                'b',
                'nglayouts_layout',
                'l',
                $query->expr()->and(
                    $query->expr()->eq('l.id', 'b.layout_id'),
                    $query->expr()->eq('l.status', 'b.status'),
                ),
            )
            ->where(
                $query->expr()->and(
                    $query->expr()->or(
                        $query->expr()->and(
                            $query->expr()->eq('ci.value_type', ':content_value_type'),
                            $query->expr()->eq('ci.value', ':content_id'),
                        ),
                        $query->expr()->and(
                            $query->expr()->eq('ci.value_type', ':location_value_type'),
                            $query->expr()->eq('ci.value', ':location_id'),
                        ),
                    ),
                    $query->expr()->eq('ci.status', ':status'),
                ),
            )
            ->orderBy('l.name', 'ASC')
            ->setParameter('status', Value::STATUS_PUBLISHED, Types::INTEGER)
            ->setParameter('content_value_type', 'ezcontent', Types::STRING)
            ->setParameter('location_value_type', 'ezlocation', Types::STRING)
            ->setParameter('content_id', $location->contentInfo->id, Types::INTEGER)
            ->setParameter('location_id', $location->id, Types::INTEGER);

        return array_map(
            fn (array $dataRow): Layout => $this->layoutService->loadLayout(Uuid::fromString($dataRow['uuid'])),
            $query->execute()->fetchAllAssociative(),
        );
    }
}
