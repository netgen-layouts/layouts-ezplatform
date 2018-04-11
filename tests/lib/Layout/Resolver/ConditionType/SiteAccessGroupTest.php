<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\ConditionType;

use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;
use Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\SiteAccessGroup;
use Netgen\BlockManager\Ez\Tests\Validator\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class SiteAccessGroupTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\SiteAccessGroup
     */
    private $conditionType;

    public function setUp()
    {
        $this->conditionType = new SiteAccessGroup(
            array(
                'eng' => array(
                    'frontend',
                ),
                'admin' => array(
                    'backend',
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::getType
     */
    public function testGetType()
    {
        $this->assertEquals('ez_site_access_group', $this->conditionType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory())
            ->getValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, $matches)
    {
        $request = Request::create('/');
        $request->attributes->set('siteaccess', new EzPublishSiteAccess('eng'));

        $this->assertEquals($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::matches
     */
    public function testMatchesWithSiteAccessWithNoGroups()
    {
        $request = Request::create('/');
        $request->attributes->set('siteaccess', new EzPublishSiteAccess('cro'));

        $this->assertFalse($this->conditionType->matches($request, array('frontend')));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::matches
     */
    public function testMatchesWithNoSiteAccess()
    {
        $request = Request::create('/');

        $this->assertFalse($this->conditionType->matches($request, array('frontend')));
    }

    /**
     * Provider for testing condition type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(array('frontend'), true),
            array(array('backend', 'frontend'), true),
            array(array('frontend', 'unknown'), false),
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
            array(array('frontend'), true),
            array(array('backend'), false),
            array(array('frontend', 'backend'), true),
            array(array('frontend', 'other'), true),
            array(array('backend', 'other'), false),
        );
    }
}
