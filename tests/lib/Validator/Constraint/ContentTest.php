<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator\Constraint;

use Netgen\Layouts\Ibexa\Validator\Constraint\Content;
use PHPUnit\Framework\TestCase;

final class ContentTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\Constraint\Content::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Content();
        self::assertSame('nglayouts_ibexa_content', $constraint->validatedBy());
    }
}
