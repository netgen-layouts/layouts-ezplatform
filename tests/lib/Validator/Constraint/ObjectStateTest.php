<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\ObjectState;
use PHPUnit\Framework\TestCase;

final class ObjectStateTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\Constraint\ObjectState::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ObjectState();
        self::assertSame('nglayouts_ibexa_object_state', $constraint->validatedBy());
    }
}
