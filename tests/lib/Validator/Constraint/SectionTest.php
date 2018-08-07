<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\Section;
use PHPUnit\Framework\TestCase;

final class SectionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\Section::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Section();
        self::assertSame('ngbm_ez_section', $constraint->validatedBy());
    }
}
