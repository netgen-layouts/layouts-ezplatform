<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class SiteAccessGroupTest extends TestCase
{
    private SiteAccessGroup $mapper;

    protected function setUp(): void
    {
        $this->mapper = new SiteAccessGroup(
            [
                'frontend' => ['eng'],
                'backend' => ['admin'],
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup::__construct
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        self::assertSame(
            [
                'choices' => ['frontend' => 'frontend', 'backend' => 'backend'],
                'choice_translation_domain' => false,
                'multiple' => true,
                'expanded' => true,
            ],
            $this->mapper->getFormOptions(),
        );
    }
}
