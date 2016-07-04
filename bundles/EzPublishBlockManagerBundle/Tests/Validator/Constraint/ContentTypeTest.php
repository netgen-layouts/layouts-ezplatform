<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Validator\Constraint;

use Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\ContentType;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Validator\Constraint\ContentType::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new ContentType();
        self::assertEquals('ngbm_ez_content_type', $constraint->validatedBy());
    }
}
