<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsEzPlatformBundle\Templating\Twig\Runtime\EzPlatformRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class EzPlatformExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'nglayouts_ez_content_name',
                [EzPlatformRuntime::class, 'getContentName'],
            ),
            new TwigFunction(
                'nglayouts_ez_location_path',
                [EzPlatformRuntime::class, 'getLocationPath'],
            ),
            new TwigFunction(
                'nglayouts_ez_content_path',
                [EzPlatformRuntime::class, 'getContentPath'],
            ),
            new TwigFunction(
                'nglayouts_ez_content_type_name',
                [EzPlatformRuntime::class, 'getContentTypeName'],
            ),
        ];
    }
}
