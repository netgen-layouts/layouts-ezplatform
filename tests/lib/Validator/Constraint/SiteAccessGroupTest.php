<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\SiteAccessGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SiteAccessGroup::class)]
final class SiteAccessGroupTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new SiteAccessGroup();
        self::assertSame('nglayouts_ibexa_site_access_group', $constraint->validatedBy());
    }
}
