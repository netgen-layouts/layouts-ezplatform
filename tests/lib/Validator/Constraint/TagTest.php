<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator\Constraint;

use Netgen\Layouts\Ez\Validator\Constraint\Tag;
use PHPUnit\Framework\TestCase;

final class TagTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ez\Validator\Constraint\Tag::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Tag();
        self::assertSame('nglayouts_ez_tags', $constraint->validatedBy());
    }
}
