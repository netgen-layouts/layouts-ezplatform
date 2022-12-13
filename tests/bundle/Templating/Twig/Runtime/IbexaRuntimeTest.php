<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Tests\Templating\Twig\Runtime;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class IbexaRuntimeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Contracts\Core\Repository\Repository,
     */
    private MockObject $repositoryMock;

    private MockObject $contentServiceMock;

    private MockObject $locationServiceMock;

    private MockObject $contentTypeServiceMock;

    private IbexaRuntime $runtime;

    protected function setUp(): void
    {
        $this->prepareRepositoryMock();

        $this->runtime = new IbexaRuntime(
            $this->repositoryMock,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::__construct
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getContentName
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadContent
     */
    public function testGetContentName(): void
    {
        $this->mockServices();

        self::assertSame('Content name 42', $this->runtime->getContentName(42));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getContentName
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadContent
     */
    public function testGetContentNameWithException(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContent')
            ->with(self::identicalTo(42))
            ->willThrowException(new Exception());

        self::assertSame('', $this->runtime->getContentName(42));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getLocationPath
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadContent
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadLocation
     */
    public function testGetLocationPath(): void
    {
        $this->mockServices();

        self::assertSame(
            [
                'Content name 102',
                'Content name 142',
                'Content name 184',
            ],
            $this->runtime->getLocationPath(22),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getLocationPath
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadContent
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadLocation
     */
    public function testGetLocationPathWithException(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(22))
            ->willThrowException(new Exception());

        self::assertSame([], $this->runtime->getLocationPath(22));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getContentPath
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getLocationPath
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadContent
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadLocation
     */
    public function testGetContentPath(): void
    {
        $this->mockServices();

        self::assertSame(
            [
                'Content name 102',
                'Content name 142',
                'Content name 184',
            ],
            $this->runtime->getContentPath(122),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getContentPath
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getLocationPath
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadContent
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadLocation
     */
    public function testGetContentPathWithException(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContent')
            ->with(self::identicalTo(22))
            ->willThrowException(new Exception());

        self::assertSame([], $this->runtime->getContentPath(22));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getContentTypeName
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadContentType
     */
    public function testGetContentTypeName(): void
    {
        $this->mockServices();

        $this->contentTypeServiceMock
            ->expects(self::any())
            ->method('loadContentTypeByIdentifier')
            ->willReturnCallback(
                static fn (string $identifier): ContentType => new ContentType(
                    [
                        'identifier' => $identifier,
                        'names' => [
                            'eng-GB' => 'English content type ' . $identifier,
                            'cro-HR' => 'Content type ' . $identifier,
                        ],
                        'mainLanguageCode' => 'cro-HR',
                    ],
                ),
            );

        self::assertSame('Content type some_type', $this->runtime->getContentTypeName('some_type'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getContentTypeName
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadContentType
     */
    public function testGetContentTypeNameWithNoTranslatedName(): void
    {
        $this->mockServices();

        $this->contentTypeServiceMock
            ->expects(self::any())
            ->method('loadContentTypeByIdentifier')
            ->willReturnCallback(
                static fn (string $identifier): ContentType => new ContentType(
                    [
                        'identifier' => $identifier,
                        'names' => [
                            'eng-GB' => 'English content type ' . $identifier,
                            'cro-HR' => 'Content type ' . $identifier,
                        ],
                        'mainLanguageCode' => 'eng-GB',
                    ],
                ),
            );

        self::assertSame('English content type some_type', $this->runtime->getContentTypeName('some_type'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::getContentTypeName
     * @covers \Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime::loadContentType
     */
    public function testGetContentTypeNameWithException(): void
    {
        $this->contentTypeServiceMock
            ->expects(self::once())
            ->method('loadContentTypeByIdentifier')
            ->with(self::identicalTo('some_type'))
            ->willThrowException(new Exception());

        self::assertSame('', $this->runtime->getContentTypeName('some_type'));
    }

    private function prepareRepositoryMock(): void
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $this->repositoryMock = $this->createPartialMock(
            Repository::class,
            [
                'sudo',
                'getContentService',
                'getLocationService',
                'getContentTypeService',
            ],
        );

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->expects(self::any())
            ->method('getLocationService')
            ->willReturn($this->locationServiceMock);

        $this->repositoryMock
            ->expects(self::any())
            ->method('getContentService')
            ->willReturn($this->contentServiceMock);

        $this->repositoryMock
            ->expects(self::any())
            ->method('getContentTypeService')
            ->willReturn($this->contentTypeServiceMock);
    }

    private function mockServices(): void
    {
        $this->locationServiceMock
            ->expects(self::any())
            ->method('loadLocation')
            ->willReturnCallback(
                static fn ($locationId): Location => new Location(
                    [
                        'path' => [1, 2, 42, 84],
                        'contentInfo' => new ContentInfo(
                            [
                                'id' => $locationId + 100,
                            ],
                        ),
                    ],
                ),
            );

        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadContent')
            ->willReturnCallback(
                static fn ($contentId): Content => new Content(
                    [
                        'versionInfo' => new VersionInfo(
                            [
                                'contentInfo' => new ContentInfo(
                                    [
                                        'mainLocationId' => $contentId - 100,
                                    ],
                                ),
                                'prioritizedNameLanguageCode' => 'eng-GB',
                                'names' => ['eng-GB' => 'Content name ' . $contentId],
                            ],
                        ),
                    ],
                ),
            );
    }
}
