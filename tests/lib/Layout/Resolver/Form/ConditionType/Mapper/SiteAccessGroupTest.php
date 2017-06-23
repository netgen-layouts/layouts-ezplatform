<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SiteAccessGroupTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new SiteAccessGroup(
            array(
                'frontend' => array('eng'),
                'backend' => array('admin'),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccessGroup::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(
                'choices' => array('frontend' => 'frontend', 'backend' => 'backend'),
                'choice_translation_domain' => false,
                'choices_as_values' => true,
                'multiple' => true,
                'expanded' => true,
            ),
            $this->mapper->mapOptions(
                $this->createMock(ConditionTypeInterface::class)
            )
        );
    }
}
