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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(IbexaRuntime::class)]
final class IbexaRuntimeTest extends TestCase
{
    private MockObject&Repository $repositoryMock;

    private MockObject&ContentService $contentServiceMock;

    private MockObject&LocationService $locationServiceMock;

    private MockObject&ContentTypeService $contentTypeServiceMock;

    private IbexaRuntime $runtime;

    protected function setUp(): void
    {
        $this->prepareRepositoryMock();

        $this->runtime = new IbexaRuntime(
            $this->repositoryMock,
        );
    }

    public function testGetContentName(): void
    {
        $this->mockServices();

        self::assertSame('Content name 42', $this->runtime->getContentName(42));
    }

    public function testGetContentNameWithException(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContent')
            ->with(self::identicalTo(42))
            ->willThrowException(new Exception());

        self::assertSame('', $this->runtime->getContentName(42));
    }

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

    public function testGetLocationPathWithException(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(22))
            ->willThrowException(new Exception());

        self::assertSame([], $this->runtime->getLocationPath(22));
    }

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

    public function testGetContentPathWithException(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadContent')
            ->with(self::identicalTo(22))
            ->willThrowException(new Exception());

        self::assertSame([], $this->runtime->getContentPath(22));
    }

    public function testGetContentTypeName(): void
    {
        $this->mockServices();

        $this->contentTypeServiceMock
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

    public function testGetContentTypeNameWithNoTranslatedName(): void
    {
        $this->mockServices();

        $this->contentTypeServiceMock
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
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->method('getLocationService')
            ->willReturn($this->locationServiceMock);

        $this->repositoryMock
            ->method('getContentService')
            ->willReturn($this->contentServiceMock);

        $this->repositoryMock
            ->method('getContentTypeService')
            ->willReturn($this->contentTypeServiceMock);
    }

    private function mockServices(): void
    {
        $this->locationServiceMock
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
