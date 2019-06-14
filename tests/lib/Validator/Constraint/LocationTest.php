<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator\Constraint;

use Netgen\Layouts\Ez\Validator\Constraint\Location;
use PHPUnit\Framework\TestCase;

final class LocationTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ez\Validator\Constraint\Location::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Location();
        self::assertSame('nglayouts_ez_location', $constraint->validatedBy());
    }
}
