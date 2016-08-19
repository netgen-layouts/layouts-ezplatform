<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Ez\Validator\SiteAccessValidator;
use Netgen\BlockManager\Ez\Validator\Constraint\SiteAccess;

class SiteAccessValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->constraint = new SiteAccess();
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        return new SiteAccessValidator(array('eng', 'cro'));
    }

    /**
     * @param int $identifier
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($identifier, $isValid)
    {
        $this->assertValid($isValid, $identifier);
    }

    public function validateDataProvider()
    {
        return array(
            array('eng', true),
            array('fre', false),
            array(5, false),
            array(null, true),
        );
    }
}
