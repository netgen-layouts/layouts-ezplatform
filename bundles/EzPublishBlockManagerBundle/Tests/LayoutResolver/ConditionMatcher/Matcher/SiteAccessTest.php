<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\ConditionMatcher\Matcher;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\ConditionMatcher\Matcher\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class SiteAccessTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * Sets up the route target tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('siteaccess', new EzPublishSiteAccess('eng'));

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\ConditionMatcher\Matcher\SiteAccess::getConditionIdentifier
     */
    public function testGetConditionIdentifier()
    {
        $conditionMatcher = new SiteAccess();

        self::assertEquals('siteaccess', $conditionMatcher->getConditionIdentifier());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\ConditionMatcher\Matcher\SiteAccess::matches
     *
     * @param string $valueIdentifier
     * @param array $values
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($valueIdentifier, array $values, $matches)
    {
        $conditionMatcher = new SiteAccess();
        $conditionMatcher->setRequestStack($this->requestStack);

        self::assertEquals($matches, $conditionMatcher->matches($valueIdentifier, $values));
    }

    /**
     * Provider for {@link self::testMatches}.
     *
     * @return array
     */
    public function matchesProvider()
    {
        return array(
            array(null, array('eng'), true),
            array(null, array('cro'), false),
            array(null, array('eng', 'cro'), true),
            array(null, array('cro', 'eng'), true),
            array(null, array('cro', 'fre'), false),
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\ConditionMatcher\Matcher\SiteAccess::matches
     */
    public function testMatchesWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $conditionMatcher = new SiteAccess();
        $conditionMatcher->setRequestStack($this->requestStack);

        self::assertEquals(false, $conditionMatcher->matches('identifier', array(42)));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\ConditionMatcher\Matcher\SiteAccess::matches
     */
    public function testMatchesWithNoSiteAccess()
    {
        // Make sure we have no siteaccess
        $this->requestStack->getCurrentRequest()->attributes->remove('siteaccess');

        $conditionMatcher = new SiteAccess();
        $conditionMatcher->setRequestStack($this->requestStack);

        self::assertEquals(false, $conditionMatcher->matches('identifier', array(42)));
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\ConditionMatcher\Matcher\SiteAccess::matches
     */
    public function testMatchesWithEmptyValues()
    {
        $conditionMatcher = new SiteAccess();
        $conditionMatcher->setRequestStack($this->requestStack);

        self::assertEquals(false, $conditionMatcher->matches('identifier', array()));
    }
}
