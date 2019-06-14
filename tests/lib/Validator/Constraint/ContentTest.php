<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator\Constraint;

use Netgen\Layouts\Ez\Validator\Constraint\Content;
use PHPUnit\Framework\TestCase;

final class ContentTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Ez\Validator\Constraint\Content::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Content();
        self::assertSame('nglayouts_ez_content', $constraint->validatedBy());
    }
}
