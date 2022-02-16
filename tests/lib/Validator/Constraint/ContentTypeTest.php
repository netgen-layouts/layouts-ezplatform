<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\ContentType;
use PHPUnit\Framework\TestCase;

final class ContentTypeTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\Constraint\ContentType::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ContentType();
        self::assertSame('nglayouts_ibexa_content_type', $constraint->validatedBy());
    }
}
