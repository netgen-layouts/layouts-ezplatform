<?php

namespace Netgen\BlockManager\Ez\Tests\Item\ValueUrlBuilder;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\BlockManager\Ez\Item\ValueUrlBuilder\ContentValueUrlBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ContentValueUrlBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGenerator;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\ContentValueUrlBuilder
     */
    private $urlBuilder;

    public function setUp()
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $this->urlBuilder = new ContentValueUrlBuilder($this->urlGenerator);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\ContentValueUrlBuilder::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\ContentValueUrlBuilder::getUrl
     */
    public function testGetUrl()
    {
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo(UrlAliasRouter::URL_ALIAS_ROUTE_NAME),
                array(
                    'contentId' => 42,
                )
            )
            ->will($this->returnValue('/content/path'));

        $this->assertEquals('/content/path', $this->urlBuilder->getUrl(new ContentInfo(array('id' => 42))));
    }
}
