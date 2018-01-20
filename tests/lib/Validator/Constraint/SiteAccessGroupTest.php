<?php

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\SiteAccessGroup;
use PHPUnit\Framework\TestCase;

final class SiteAccessGroupTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\SiteAccessGroup::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new SiteAccessGroup();
        $this->assertEquals('ngbm_ez_site_access_group', $constraint->validatedBy());
    }
}
