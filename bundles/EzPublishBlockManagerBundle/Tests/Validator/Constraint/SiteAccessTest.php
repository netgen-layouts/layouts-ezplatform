<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Validator\Constraint;

use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\SiteAccess;
use PHPUnit\Framework\TestCase;

class SiteAccessTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\SiteAccess::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new SiteAccess();
        self::assertEquals('ngbm_ez_site_access', $constraint->validatedBy());
    }
}
