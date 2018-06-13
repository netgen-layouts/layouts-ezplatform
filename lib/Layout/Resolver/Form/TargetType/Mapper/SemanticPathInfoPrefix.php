<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class SemanticPathInfoPrefix extends Mapper
{
    public function getFormType()
    {
        return TextType::class;
    }
}
