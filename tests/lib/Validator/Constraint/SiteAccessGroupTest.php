<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator\Constraint;

use Netgen\Layouts\Ez\Validator\Constraint\SiteAccessGroup;
use PHPUnit\Framework\TestCase;

final class SiteAccessGroupTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ez\Validator\Constraint\SiteAccessGroup::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new SiteAccessGroup();
        self::assertSame('nglayouts_ez_site_access_group', $constraint->validatedBy());
    }
}
