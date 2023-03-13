<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\Content;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Content::class)]
final class ContentTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new Content();
        self::assertSame('nglayouts_ibexa_content', $constraint->validatedBy());
    }
}
