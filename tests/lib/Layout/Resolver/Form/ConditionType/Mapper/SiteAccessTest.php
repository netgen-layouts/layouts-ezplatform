<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class SiteAccessTest extends TestCase
{
    private SiteAccess $mapper;

    protected function setUp(): void
    {
        $this->mapper = new SiteAccess(['cro', 'eng']);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::__construct
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        self::assertSame(
            [
                'choices' => ['cro' => 'cro', 'eng' => 'eng'],
                'choice_translation_domain' => false,
                'multiple' => true,
                'expanded' => true,
            ],
            $this->mapper->getFormOptions(),
        );
    }
}
