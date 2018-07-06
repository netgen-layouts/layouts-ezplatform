<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Item\ValueUrlGenerator;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\BlockManager\Ez\Item\ValueUrlGenerator\ContentValueUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ContentValueUrlGeneratorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGeneratorMock;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueUrlGenerator\ContentValueUrlGenerator
     */
    private $urlGenerator;

    public function setUp(): void
    {
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->urlGenerator = new ContentValueUrlGenerator($this->urlGeneratorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlGenerator\ContentValueUrlGenerator::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlGenerator\ContentValueUrlGenerator::generate
     */
    public function testGenerate(): void
    {
        $this->urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo(UrlAliasRouter::URL_ALIAS_ROUTE_NAME),
                $this->identicalTo(['contentId' => 42])
            )
            ->will($this->returnValue('/content/path'));

        $this->assertSame('/content/path', $this->urlGenerator->generate(new ContentInfo(['id' => 42])));
    }
}
