<?php

namespace Netgen\BlockManager\Ez\Tests\Validator\Constraint;

use Netgen\BlockManager\Ez\Validator\Constraint\Content;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Validator\Constraint\Content::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new Content();
        self::assertEquals('ngbm_ezcontent', $constraint->validatedBy());
    }
}
