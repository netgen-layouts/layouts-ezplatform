<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use Netgen\BlockManager\Ez\Validator\Constraint\ContentType;
use Netgen\BlockManager\Ez\Validator\ContentTypeValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContentTypeValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    public function setUp()
    {
        parent::setUp();

        $this->constraint = new ContentType();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidatorInterface
     */
    public function getValidator()
    {
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, array('sudo', 'getContentTypeService'));

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function ($callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getContentTypeService')
            ->will($this->returnValue($this->contentTypeServiceMock));

        return new ContentTypeValidator($this->repositoryMock);
    }

    /**
     * @param string|null $identifier
     * @param array $groups
     * @param array $allowedTypes
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($identifier, $groups, $allowedTypes, $isValid)
    {
        if ($identifier !== null) {
            $this->contentTypeServiceMock
                ->expects($this->once())
                ->method('loadContentTypeByIdentifier')
                ->with($this->equalTo($identifier))
                ->will(
                    $this->returnCallback(
                        function () use ($identifier, $groups) {
                            if (!is_string($identifier) || $identifier === 'unknown') {
                                throw new NotFoundException('content type', $identifier);
                            }

                            return new EzContentType(
                                array(
                                    'identifier' => $identifier,
                                    'contentTypeGroups' => array_map(
                                        function ($group) {
                                            return new ContentTypeGroup(
                                                array(
                                                    'identifier' => $group,
                                                )
                                            );
                                        },
                                        $groups
                                    ),
                                )
                            );
                        }
                    )
                );
        }

        $this->constraint->allowedTypes = $allowedTypes;
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\ContentType", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "array", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidAllowedTypes()
    {
        $this->constraint->allowedTypes = 42;
        $this->assertValid(true, 'article');
    }

    public function validateDataProvider()
    {
        return array(
            array('article', array('group1'), array(), true),
            array('article', array('group1'), array('group2' => true), true),
            array('article', array('group1'), array('group1' => true), true),
            array('article', array('group1'), array('group1' => false), false),
            array('article', array('group1'), array('group1' => array()), false),
            array('article', array('group1'), array('group1' => array('article')), true),
            array('article', array('group1'), array('group1' => array('news')), false),
            array('article', array('group1'), array('group1' => array('article', 'news')), true),
            array('unknown', array('group1'), array(), false),
            array(null, array('group1'), array(), true),
        );
    }
}
