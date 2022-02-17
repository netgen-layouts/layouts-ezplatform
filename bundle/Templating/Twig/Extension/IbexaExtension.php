<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Extension;

use Netgen\Bundle\LayoutsIbexaBundle\Templating\Twig\Runtime\IbexaRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class IbexaExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'nglayouts_ibexa_content_name',
                [IbexaRuntime::class, 'getContentName'],
            ),
            new TwigFunction(
                'nglayouts_ibexa_location_path',
                [IbexaRuntime::class, 'getLocationPath'],
            ),
            new TwigFunction(
                'nglayouts_ibexa_content_type_name',
                [IbexaRuntime::class, 'getContentTypeName'],
            ),
        ];
    }
}
