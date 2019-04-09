<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator\Constraint;

use Netgen\Layouts\Ez\Validator\Constraint\Section;
use PHPUnit\Framework\TestCase;

final class SectionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ez\Validator\Constraint\Section::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Section();
        self::assertSame('nglayouts_ez_section', $constraint->validatedBy());
    }
}
