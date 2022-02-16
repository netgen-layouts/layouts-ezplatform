<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\Tag;
use PHPUnit\Framework\TestCase;

final class TagTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\Constraint\Tag::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Tag();
        self::assertSame('nglayouts_netgen_tags', $constraint->validatedBy());
    }
}
