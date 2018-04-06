<?php

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\ObjectState;
use PHPUnit\Framework\TestCase;

final class ObjectStateTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\ObjectState::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new ObjectState();
        $this->assertEquals('ngbm_ez_object_state', $constraint->validatedBy());
    }
}
