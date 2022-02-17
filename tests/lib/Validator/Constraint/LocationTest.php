<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\Location;
use PHPUnit\Framework\TestCase;

final class LocationTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\Constraint\Location::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Location();
        self::assertSame('nglayouts_ibexa_location', $constraint->validatedBy());
    }
}
