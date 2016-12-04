<?php

namespace Netgen\BlockManager\Ez\Tests\Item\ValueUrlBuilder;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Netgen\BlockManager\Ez\Item\ValueUrlBuilder\ContentValueUrlBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

class ContentValueUrlBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $router;

    /**
     * @var \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\ContentValueUrlBuilder
     */
    protected $urlBuilder;

    public function setUp()
    {
        $this->router = $this->createMock(RouterInterface::class);

        $this->urlBuilder = new ContentValueUrlBuilder($this->router);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\ContentValueUrlBuilder::__construct
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\ContentValueUrlBuilder::getValueType
     */
    public function testGetValueType()
    {
        $this->assertEquals('ezcontent', $this->urlBuilder->getValueType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Item\ValueUrlBuilder\ContentValueUrlBuilder::getUrl
     */
    public function testGetUrl()
    {
        $this->router
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
