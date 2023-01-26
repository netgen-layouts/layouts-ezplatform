<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Item\ValueUrlGenerator;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\Layouts\Ibexa\Item\ValueUrlGenerator\ContentValueUrlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ContentValueUrlGeneratorTest extends TestCase
{
    private MockObject $urlGeneratorMock;

    private ContentValueUrlGenerator $urlGenerator;

    protected function setUp(): void
    {
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->urlGenerator = new ContentValueUrlGenerator($this->urlGeneratorMock);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueUrlGenerator\ContentValueUrlGenerator::__construct
     * @covers \Netgen\Layouts\Ibexa\Item\ValueUrlGenerator\ContentValueUrlGenerator::generateDefaultUrl
     */
    public function testGenerateDefaultUrl(): void
    {
        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(
                self::identicalTo(UrlAliasRouter::URL_ALIAS_ROUTE_NAME),
                self::identicalTo(['contentId' => 42]),
            )
            ->willReturn('/content/path');

        self::assertSame('/content/path', $this->urlGenerator->generateDefaultUrl(new ContentInfo(['id' => 42])));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueUrlGenerator\ContentValueUrlGenerator::generateAdminUrl
     */
    public function testGenerateAdminUrl(): void
    {
        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(
                self::identicalTo('ibexa.content.view'),
                self::identicalTo(['contentId' => 42]),
            )
            ->willReturn('/admin/content/path');

        self::assertSame('/admin/content/path', $this->urlGenerator->generateAdminUrl(new ContentInfo(['id' => 42])));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueUrlGenerator\ContentValueUrlGenerator::generate
     */
    public function testGenerate(): void
    {
        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(
                self::identicalTo(UrlAliasRouter::URL_ALIAS_ROUTE_NAME),
                self::identicalTo(['contentId' => 42]),
            )
            ->willReturn('/content/path');

        self::assertSame('/content/path', $this->urlGenerator->generate(new ContentInfo(['id' => 42])));
    }
}
