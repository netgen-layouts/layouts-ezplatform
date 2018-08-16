<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\SPI\Persistence\Content\Section\Handler;
use Netgen\BlockManager\Ez\Parameters\ParameterType as EzParameterType;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterType;

trait SectionFilterTrait
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Section\Handler
     */
    private $sectionHandler;

    /**
     * Sets the section handler used by the trait.
     */
    private function setSectionHandler(Handler $handler): void
    {
        $this->sectionHandler = $handler;
    }

    /**
     * Builds the parameters for filtering by sections.
     */
    private function buildSectionFilterParameters(ParameterBuilderInterface $builder, array $groups = []): void
    {
        $builder->add(
            'filter_by_section',
            ParameterType\Compound\BooleanType::class,
            [
                'groups' => $groups,
            ]
        );

        $builder->get('filter_by_section')->add(
            'sections',
            EzParameterType\SectionType::class,
            [
                'multiple' => true,
                'groups' => $groups,
            ]
        );
    }

    /**
     * Returns the criteria used to filter content by section.
     */
    private function getSectionFilterCriteria(ParameterCollectionInterface $parameterCollection): ?Criterion
    {
        if ($parameterCollection->getParameter('filter_by_section')->getValue() !== true) {
            return null;
        }

        $sections = $parameterCollection->getParameter('sections')->getValue();
        if (empty($sections)) {
            return null;
        }

        return new Criterion\SectionId($this->getSectionIds($sections));
    }

    /**
     * Returns section IDs for all provided section identifiers.
     */
    private function getSectionIds(array $sectionIdentifiers): array
    {
        $idList = [];

        foreach ($sectionIdentifiers as $identifier) {
            try {
                $section = $this->sectionHandler->loadByIdentifier($identifier);
                $idList[] = $section->id;
            } catch (NotFoundException $e) {
                continue;
            }
        }

        return $idList;
    }
}
