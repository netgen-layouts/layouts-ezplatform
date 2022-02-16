<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\SiteAccessGroup;
use PHPUnit\Framework\TestCase;

final class SiteAccessGroupTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\Constraint\SiteAccessGroup::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new SiteAccessGroup();
        self::assertSame('nglayouts_ibexa_site_access_group', $constraint->validatedBy());
    }
}
