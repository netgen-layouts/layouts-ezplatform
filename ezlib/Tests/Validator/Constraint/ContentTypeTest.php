<?php

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\ContentType;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\ContentType::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new ContentType();
        self::assertEquals('ngbm_ez_content_type', $constraint->validatedBy());
    }
}
