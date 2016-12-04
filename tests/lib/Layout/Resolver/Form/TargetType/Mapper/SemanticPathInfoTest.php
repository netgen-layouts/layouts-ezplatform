<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfo;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SemanticPathInfoTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new SemanticPathInfo();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfo::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(TextType::class, $this->mapper->getFormType());
    }
}
