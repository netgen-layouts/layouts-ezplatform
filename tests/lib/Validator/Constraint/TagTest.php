<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\Tag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Tag::class)]
final class TagTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new Tag();
        self::assertSame('nglayouts_netgen_tags', $constraint->validatedBy());
    }
}
