<?php

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\Location;
use PHPUnit\Framework\TestCase;

final class LocationTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\Location::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new Location();
        $this->assertEquals('ngbm_ezlocation', $constraint->validatedBy());
    }
}
