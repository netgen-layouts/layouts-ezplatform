<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator\Constraint;

use Netgen\Layouts\Ez\Validator\Constraint\ObjectState;
use PHPUnit\Framework\TestCase;

final class ObjectStateTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ez\Validator\Constraint\ObjectState::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ObjectState();
        self::assertSame('nglayouts_ez_object_state', $constraint->validatedBy());
    }
}
