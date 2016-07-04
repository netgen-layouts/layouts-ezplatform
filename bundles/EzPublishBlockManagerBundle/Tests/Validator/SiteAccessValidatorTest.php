<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Validator;

use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\SiteAccessValidator;
use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\SiteAccess;

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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Validator\SiteAccessValidator::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Validator\SiteAccessValidator::validate
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
