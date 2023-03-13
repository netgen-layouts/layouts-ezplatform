<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\ContentType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContentType::class)]
final class ContentTypeTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new ContentType();
        self::assertSame('nglayouts_ibexa_content_type', $constraint->validatedBy());
    }
}
