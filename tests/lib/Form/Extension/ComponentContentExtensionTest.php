<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Form\Extension;

use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Ibexa\Block\BlockDefinition\Handler\ComponentHandler;
use Netgen\Layouts\Ibexa\Form\Extension\ComponentContentExtension;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

#[CoversClass(ComponentContentExtension::class)]
final class ComponentContentExtensionTest extends TestCase
{
    private ComponentContentExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new ComponentContentExtension();
    }

    public function testGetExtendedType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->extension->getExtendedType());
    }

    public function testGetExtendedTypes(): void
    {
        self::assertSame([ContentBrowserType::class], $this->extension::getExtendedTypes());
    }

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

        self::assertContains('ibexa_component_content', $formView->vars['block_prefixes']);
    }

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

        self::assertNotContains('ibexa_component_content', $formView->vars['block_prefixes']);
    }

    public function testBuildViewWithoutBlock(): void
    {
        $formView = new FormView();
        $formView->vars['block_prefixes'] = [];

        $this->extension->buildView($formView, $this->createMock(FormInterface::class), []);

        self::assertNotContains('ibexa_component_content', $formView->vars['block_prefixes']);
    }
}
