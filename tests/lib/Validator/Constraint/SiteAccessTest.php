<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\SiteAccess;
use PHPUnit\Framework\TestCase;

final class SiteAccessTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\Constraint\SiteAccess::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new SiteAccess();
        self::assertSame('nglayouts_ibexa_site_access', $constraint->validatedBy());
    }
}
