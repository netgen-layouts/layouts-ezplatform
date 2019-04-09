<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator\Constraint;

use Netgen\Layouts\Ez\Validator\Constraint\SiteAccess;
use PHPUnit\Framework\TestCase;

final class SiteAccessTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ez\Validator\Constraint\SiteAccess::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new SiteAccess();
        self::assertSame('nglayouts_ez_site_access', $constraint->validatedBy());
    }
}
