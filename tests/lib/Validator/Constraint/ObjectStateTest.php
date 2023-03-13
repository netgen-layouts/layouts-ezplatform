<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\ObjectState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ObjectState::class)]
final class ObjectStateTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new ObjectState();
        self::assertSame('nglayouts_ibexa_object_state', $constraint->validatedBy());
    }
}
