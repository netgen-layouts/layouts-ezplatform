<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator\Constraint;

use Netgen\Layouts\Ez\Validator\Constraint\ContentType;
use PHPUnit\Framework\TestCase;

final class ContentTypeTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ez\Validator\Constraint\ContentType::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ContentType();
        self::assertSame('nglayouts_ez_content_type', $constraint->validatedBy());
    }
}
