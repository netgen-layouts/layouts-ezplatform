<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SiteAccessTest extends TestCase
{
    use ChoicesAsValuesTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new SiteAccess(array('cro', 'eng'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::getFormOptions
     */
    public function testGetFormOptions()
    {
        $this->assertEquals(
            array(
                'choices' => array('cro' => 'cro', 'eng' => 'eng'),
                'choice_translation_domain' => false,
                'multiple' => true,
                'expanded' => true,
            ) + $this->getChoicesAsValuesOption(),
            $this->mapper->getFormOptions()
        );
    }
}
