<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Validator\Constraint;

use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\Location;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\Location::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new Location();
        self::assertEquals('ngbm_ezlocation', $constraint->validatedBy());
    }
}
