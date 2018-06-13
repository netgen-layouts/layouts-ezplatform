<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\ContentType;
use PHPUnit\Framework\TestCase;

final class ContentTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\ContentType::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ContentType();
        $this->assertEquals('ngbm_ez_content_type', $constraint->validatedBy());
    }
}
