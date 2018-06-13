<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class SiteAccessTest extends TestCase
{
    use ChoicesAsValuesTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new SiteAccess(['cro', 'eng']);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertEquals(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        $this->assertEquals(
            [
                'choices' => ['cro' => 'cro', 'eng' => 'eng'],
                'choice_translation_domain' => false,
                'multiple' => true,
                'expanded' => true,
            ] + $this->getChoicesAsValuesOption(),
            $this->mapper->getFormOptions()
        );
    }
}
