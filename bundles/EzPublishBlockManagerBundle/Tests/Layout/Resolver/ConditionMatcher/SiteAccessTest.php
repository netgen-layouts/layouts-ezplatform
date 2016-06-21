<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\ConditionType;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzPublishSiteAccess;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class SiteAccessTest extends TestCase
{
    use RequestStackAwareTrait;

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

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->conditionType = new SiteAccess();
        $this->conditionType->setRequestStack($this->requestStack);
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
}
