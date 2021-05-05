<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Item\ValueUrlGenerator;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\Layouts\Ez\Item\ValueUrlGenerator\ContentValueUrlGenerator;
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
     * @covers \Netgen\Layouts\Ez\Item\ValueUrlGenerator\ContentValueUrlGenerator::__construct
     * @covers \Netgen\Layouts\Ez\Item\ValueUrlGenerator\ContentValueUrlGenerator::generate
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
