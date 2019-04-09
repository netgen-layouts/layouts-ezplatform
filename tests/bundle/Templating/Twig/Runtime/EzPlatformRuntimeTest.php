<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Templating\Twig\Runtime;

use Exception;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime;
use PHPUnit\Framework\TestCase;

final class EzPlatformRuntimeTest extends TestCase
{
    /**
     * @var \eZ\Publish\API\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime
     */
    private $runtime;

    public function setUp(): void
    {
        $this->prepareRepositoryMock();

        $this->runtime = new EzPlatformRuntime(
            $this->repositoryMock
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::getContentName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::loadVersionInfo
     */
    public function testGetContentName(): void
    {
        $this->mockServices();

        self::assertSame('Content name 42', $this->runtime->getContentName(42));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::getContentName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::loadVersionInfo
     */
    public function testGetContentNameWithException(): void
    {
        $this->contentServiceMock
            ->expects(self::once())
            ->method('loadVersionInfoById')
            ->with(self::identicalTo(42))
            ->willThrowException(new Exception());

        self::assertSame('', $this->runtime->getContentName(42));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::getLocationPath
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::loadLocation
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::loadVersionInfo
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
            $this->runtime->getLocationPath(22)
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::getLocationPath
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::loadLocation
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::loadVersionInfo
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::getContentTypeName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::loadContentType
     */
    public function testGetContentTypeName(): void
    {
        $this->mockServices();

        $this->contentTypeServiceMock
            ->expects(self::any())
            ->method('loadContentTypeByIdentifier')
            ->willReturnCallback(
                static function (string $identifier): ContentType {
                    return new ContentType(
                        [
                            'identifier' => $identifier,
                            'names' => [
                                'eng-GB' => 'English content type ' . $identifier,
                                'cro-HR' => 'Content type ' . $identifier,
                            ],
                            'mainLanguageCode' => 'cro-HR',
                        ]
                    );
                }
            );

        self::assertSame('Content type some_type', $this->runtime->getContentTypeName('some_type'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::getContentTypeName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::loadContentType
     */
    public function testGetContentTypeNameWithNoTranslatedName(): void
    {
        $this->mockServices();

        $this->contentTypeServiceMock
            ->expects(self::any())
            ->method('loadContentTypeByIdentifier')
            ->willReturnCallback(
                static function (string $identifier): ContentType {
                    return new ContentType(
                        [
                            'identifier' => $identifier,
                            'names' => [
                                'eng-GB' => 'English content type ' . $identifier,
                                'cro-HR' => 'Content type ' . $identifier,
                            ],
                            'mainLanguageCode' => 'eng-GB',
                        ]
                    );
                }
            );

        self::assertSame('English content type some_type', $this->runtime->getContentTypeName('some_type'));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::getContentTypeName
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime::loadContentType
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
            ]
        );

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                function (callable $callback) {
                    return $callback($this->repositoryMock);
                }
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
                static function ($locationId): Location {
                    return new Location(
                        [
                            'path' => [1, 2, 42, 84],
                            'contentInfo' => new ContentInfo(
                                [
                                    'id' => $locationId + 100,
                                ]
                            ),
                        ]
                    );
                }
            );

        $this->contentServiceMock
            ->expects(self::any())
            ->method('loadVersionInfoById')
            ->willReturnCallback(
                static function ($contentId): VersionInfo {
                    return new VersionInfo(
                        [
                            'prioritizedNameLanguageCode' => 'eng-GB',
                            'names' => ['eng-GB' => 'Content name ' . $contentId],
                        ]
                    );
                }
            );
    }
}
