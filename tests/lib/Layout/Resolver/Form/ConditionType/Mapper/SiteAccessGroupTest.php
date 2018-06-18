<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class SiteAccessGroupTest extends TestCase
{
    use ChoicesAsValuesTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new SiteAccessGroup(
            [
                'frontend' => ['eng'],
                'backend' => ['admin'],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertSame(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        $this->assertSame(
            [
                'choices' => ['frontend' => 'frontend', 'backend' => 'backend'],
                'choice_translation_domain' => false,
                'multiple' => true,
                'expanded' => true,
            ] + $this->getChoicesAsValuesOption(),
            $this->mapper->getFormOptions()
        );
    }
}
