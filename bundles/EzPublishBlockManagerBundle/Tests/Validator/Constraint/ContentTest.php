<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Validator\Constraint;

use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\Content;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\Content::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new Content();
        self::assertEquals('ngbm_ezcontent', $constraint->validatedBy());
    }
}
