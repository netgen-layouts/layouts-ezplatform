<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPlatformRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class EzPlatformExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ngbm_ezcontent_name',
                [EzPlatformRuntime::class, 'getContentName']
            ),
            new TwigFunction(
                'ngbm_ezlocation_path',
                [EzPlatformRuntime::class, 'getLocationPath']
            ),
            new TwigFunction(
                'ngbm_ez_content_type_name',
                [EzPlatformRuntime::class, 'getContentTypeName']
            ),
        ];
    }
}
