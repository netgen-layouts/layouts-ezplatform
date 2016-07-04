<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Ez\Validator\LocationValidator;
use Netgen\BlockManager\Ez\Validator\Constraint\Location;

class LocationValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->constraint = new Location();
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        $this->repositoryMock = $this->createMock(Repository::class);

        return new LocationValidator($this->repositoryMock);
    }

    /**
     * @param int $locationId
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($locationId, $isValid)
    {
        if ($locationId !== null) {
            $this->repositoryMock
                ->expects($this->once())
                ->method('sudo')
                ->will(
                    $this->returnCallback(
                        function () use ($locationId) {
                            if (!is_int($locationId) || $locationId <= 0 || $locationId > 20) {
                                throw new NotFoundException('location', $locationId);
                            }
                        }
                    )
                );
        }

        $this->assertValid($isValid, $locationId);
    }

    public function validateDataProvider()
    {
        return array(
            array(12, true),
            array(25, false),
            array(-12, false),
            array(0, false),
            array('12', false),
            array(null, true),
        );
    }
}
