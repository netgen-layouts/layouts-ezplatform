<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\Location;
use PHPUnit\Framework\TestCase;

final class LocationTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\Location::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Location();
        self::assertSame('ngbm_ezlocation', $constraint->validatedBy());
    }
}
