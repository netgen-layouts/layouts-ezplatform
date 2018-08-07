<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\ObjectState;
use PHPUnit\Framework\TestCase;

final class ObjectStateTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\ObjectState::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ObjectState();
        self::assertSame('ngbm_ez_object_state', $constraint->validatedBy());
    }
}
