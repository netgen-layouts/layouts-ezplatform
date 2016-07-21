<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\ConditionType;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType
     */
    protected $conditionType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeServiceMock;

    /**
     * Sets up the route target tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('contentId', 42);

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $this->repositoryMock = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getContentTypeService'))
            ->getMock();

        $this->repositoryMock
            ->expects($this->any())
            ->method('getContentTypeService')
            ->will($this->returnValue($this->contentTypeServiceMock));

        $this->conditionType = new ContentType(
            $this->contentServiceMock,
            $this->contentTypeServiceMock
        );

        $this->conditionType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::getType
     */
    public function testGetType()
    {
        $this->assertEquals('ez_content_type', $this->conditionType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        if ($value !== null) {
            foreach ($value as $index => $valueItem) {
                $this->contentTypeServiceMock
                    ->expects($this->at($index))
                    ->method('loadContentTypeByIdentifier')
                    ->with($this->equalTo($valueItem))
                    ->will(
                        $this->returnCallback(
                            function () use ($valueItem) {
                                if (!is_string($valueItem) || !in_array($valueItem, array('article', 'news'))) {
                                    throw new NotFoundException('content type', $valueItem);
                                }
                            }
                        )
                    );
            }
        }

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        $this->assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, $matches)
    {
        $this->contentServiceMock
            ->expects($this->any())
            ->method('loadContentInfo')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new ContentInfo(array('contentTypeId' => 24))));

        $this->contentTypeServiceMock
            ->expects($this->any())
            ->method('loadContentType')
            ->with($this->equalTo(24))
            ->will(
                $this->returnValue(
                    new EzContentType(
                        array(
                            'identifier' => 'article',
                            'fieldDefinitions' => array(),
                        )
                    )
                )
            );

        $this->assertEquals($matches, $this->conditionType->matches($value));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::matches
     */
    public function testMatchesWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $this->assertFalse($this->conditionType->matches(array('article')));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\ContentType::matches
     */
    public function testMatchesWithNoContent()
    {
        // Make sure we have no content ID
        $this->requestStack->getCurrentRequest()->attributes->remove('contentId');

        $this->assertFalse($this->conditionType->matches(array('article')));
    }

    /**
     * Provider for testing condition type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(array('article'), true),
            array(array('article', 'news'), true),
            array(array('article', 'unknown'), false),
            array(array('unknown'), false),
            array(array(), false),
            array(null, false),
        );
    }

    /**
     * Provider for {@link self::testMatches}.
     *
     * @return array
     */
    public function matchesProvider()
    {
        return array(
            array('not_array', false),
            array(array(), false),
            array(array('article'), true),
            array(array('news'), false),
            array(array('article', 'news'), true),
            array(array('news', 'article'), true),
            array(array('news', 'video'), false),
        );
    }
}
