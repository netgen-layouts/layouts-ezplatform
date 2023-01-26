<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Item\ValueUrlGenerator;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Ibexa\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Ibexa\Item\ValueUrlGenerator\LocationValueUrlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocationValueUrlGeneratorTest extends TestCase
{
    private MockObject $urlGeneratorMock;

    private LocationValueUrlGenerator $urlGenerator;

    protected function setUp(): void
    {
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->urlGenerator = new LocationValueUrlGenerator($this->urlGeneratorMock);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueUrlGenerator\LocationValueUrlGenerator::__construct
     * @covers \Netgen\Layouts\Ibexa\Item\ValueUrlGenerator\LocationValueUrlGenerator::generateDefaultUrl
     */
    public function testGenerateDefaultUrl(): void
    {
        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(
                self::identicalTo(UrlAliasRouter::URL_ALIAS_ROUTE_NAME),
                self::identicalTo(['locationId' => 42]),
            )
            ->willReturn('/location/path');

        self::assertSame('/location/path', $this->urlGenerator->generateDefaultUrl(new Location(['id' => 42])));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueUrlGenerator\LocationValueUrlGenerator::generateAdminUrl
     */
    public function testGenerateAdminUrl(): void
    {
        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(
                self::identicalTo('ibexa.content.view'),
                self::identicalTo(['contentId' => 24, 'locationId' => 42]),
            )
            ->willReturn('/admin/location/path');

        $location = new Location(['id' => 42, 'contentInfo' => new ContentInfo(['id' => 24])]);

        self::assertSame('/admin/location/path', $this->urlGenerator->generateAdminUrl($location));
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Item\ValueUrlGenerator\LocationValueUrlGenerator::generate
     */
    public function testGenerate(): void
    {
        $this->urlGeneratorMock
            ->expects(self::once())
            ->method('generate')
            ->with(
                self::identicalTo(UrlAliasRouter::URL_ALIAS_ROUTE_NAME),
                self::identicalTo(['locationId' => 42]),
            )
            ->willReturn('/location/path');

        self::assertSame('/location/path', $this->urlGenerator->generate(new Location(['id' => 42])));
    }
}
