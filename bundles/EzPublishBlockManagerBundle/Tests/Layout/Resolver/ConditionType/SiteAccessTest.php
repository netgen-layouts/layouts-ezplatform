<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\ConditionType;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class SiteAccessTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType\SiteAccess
     */
    protected $conditionType;

    /**
     * Sets up the route target tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('siteaccess', new EzPublishSiteAccess('eng'));

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->conditionType = new SiteAccess(array('cro', 'eng', 'admin'));
        $this->conditionType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType\SiteAccess::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType\SiteAccess::getType
     */
    public function testGetType()
    {
        self::assertEquals('ezsiteaccess', $this->conditionType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType\SiteAccess::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        self::assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType\SiteAccess::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, $matches)
    {
        self::assertEquals($matches, $this->conditionType->matches($value));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType\SiteAccess::matches
     */
    public function testMatchesWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertFalse($this->conditionType->matches(array('eng')));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType\SiteAccess::matches
     */
    public function testMatchesWithNoSiteAccess()
    {
        // Make sure we have no siteaccess
        $this->requestStack->getCurrentRequest()->attributes->remove('siteaccess');

        self::assertFalse($this->conditionType->matches(array('eng')));
    }

    /**
     * Provider for testing condition type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(array('cro'), true),
            array(array('cro', 'eng'), true),
            array(array('cro', 'unknown'), false),
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
            array(array('eng'), true),
            array(array('cro'), false),
            array(array('eng', 'cro'), true),
            array(array('cro', 'eng'), true),
            array(array('cro', 'fre'), false),
        );
    }
}
