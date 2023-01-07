<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Form\Extension;

use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\Ez\Form\Extension\BlockPrefixExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class BlockPrefixExtensionTest extends TestCase
{
    private BlockPrefixExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new BlockPrefixExtension();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\Extension\BlockPrefixExtension::getExtendedType
     */
    public function testGetExtendedType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->extension->getExtendedType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\Extension\BlockPrefixExtension::getExtendedTypes
     */
    public function testGetExtendedTypes(): void
    {
        self::assertSame([ContentBrowserType::class], $this->extension::getExtendedTypes());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\Extension\BlockPrefixExtension::buildView
     */
    public function testBuildView(): void
    {
        $formView = new FormView();
        $formView->vars['block_prefixes'] = [];

        $this->extension->buildView(
            $formView,
            $this->createMock(FormInterface::class),
            [
                'block_prefix' => 'custom_prefix',
            ],
        );

        self::assertContains('custom_prefix', $formView->vars['block_prefixes']);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\Extension\BlockPrefixExtension::buildView
     */
    public function testBuildViewWithoutBlockPrefix(): void
    {
        $formView = new FormView();
        $formView->vars['block_prefixes'] = [];

        $this->extension->buildView(
            $formView,
            $this->createMock(FormInterface::class),
            [
                'block_prefix' => null,
            ],
        );

        self::assertSame([], $formView->vars['block_prefixes']);
    }
}
