<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class EzPublishExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ngbm_ezcontent_name',
                [EzPublishRuntime::class, 'getContentName']
            ),
            new TwigFunction(
                'ngbm_ezlocation_path',
                [EzPublishRuntime::class, 'getLocationPath']
            ),
            new TwigFunction(
                'ngbm_ez_content_type_name',
                [EzPublishRuntime::class, 'getContentTypeName']
            ),
        ];
    }
}
