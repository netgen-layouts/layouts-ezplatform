<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\Content;
use PHPUnit\Framework\TestCase;

final class ContentTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\Content::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new Content();
        self::assertSame('ngbm_ezcontent', $constraint->validatedBy());
    }
}
