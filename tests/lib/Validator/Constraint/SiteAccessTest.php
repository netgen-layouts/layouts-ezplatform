<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\SiteAccess;
use PHPUnit\Framework\TestCase;

final class SiteAccessTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\SiteAccess::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new SiteAccess();
        $this->assertEquals('ngbm_ez_site_access', $constraint->validatedBy());
    }
}
