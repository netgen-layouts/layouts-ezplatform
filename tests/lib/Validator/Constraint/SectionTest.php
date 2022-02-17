<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\Section;
use PHPUnit\Framework\TestCase;

final class SectionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\Constraint\Section::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Section();
        self::assertSame('nglayouts_ibexa_section', $constraint->validatedBy());
    }
}
