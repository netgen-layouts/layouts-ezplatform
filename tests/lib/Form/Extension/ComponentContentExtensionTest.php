<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Form\Extension;

use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Ez\Block\BlockDefinition\Handler\ComponentHandler;
use Netgen\Layouts\Ez\Form\Extension\ComponentContentExtension;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class ComponentContentExtensionTest extends TestCase
{
    private ComponentContentExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new ComponentContentExtension();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\Extension\ComponentContentExtension::getExtendedType
     */
    public function testGetExtendedType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->extension->getExtendedType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\Extension\ComponentContentExtension::getExtendedTypes
     */
    public function testGetExtendedTypes(): void
    {
        self::assertSame([ContentBrowserType::class], $this->extension::getExtendedTypes());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\Extension\ComponentContentExtension::buildView
     */
    public function testBuildView(): void
    {
        $formView = new FormView();
        $formView->vars['block_prefixes'] = [];

        $formView->parent = new FormView();
        $formView->parent->parent = new FormView();

        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'handler' => new ComponentHandler(),
                    ],
                ),
            ],
        );

        $formView->parent->parent->vars['block'] = $block;

        $this->extension->buildView($formView, $this->createMock(FormInterface::class), []);

        self::assertContains('ezcomponent_content', $formView->vars['block_prefixes']);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\Extension\ComponentContentExtension::buildView
     */
    public function testBuildViewWithWrongBlock(): void
    {
        $formView = new FormView();
        $formView->vars['block_prefixes'] = [];

        $formView->parent = new FormView();
        $formView->parent->parent = new FormView();

        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'handler' => new BlockDefinitionHandler(),
                    ],
                ),
            ],
        );

        $formView->parent->parent->vars['block'] = $block;

        $this->extension->buildView($formView, $this->createMock(FormInterface::class), []);

        self::assertNotContains('ezcomponent_content', $formView->vars['block_prefixes']);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\Extension\ComponentContentExtension::buildView
     */
    public function testBuildViewWithoutBlock(): void
    {
        $formView = new FormView();
        $formView->vars['block_prefixes'] = [];

        $this->extension->buildView($formView, $this->createMock(FormInterface::class), []);

        self::assertNotContains('ezcomponent_content', $formView->vars['block_prefixes']);
    }
}
