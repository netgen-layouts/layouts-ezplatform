<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class EzPublishExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'ngbm_ezcontent_name',
                array(EzPublishRuntime::class, 'getContentName')
            ),
            new TwigFunction(
                'ngbm_ezlocation_path',
                array(EzPublishRuntime::class, 'getLocationPath')
            ),
            new TwigFunction(
                'ngbm_ez_content_type_name',
                array(EzPublishRuntime::class, 'getContentTypeName')
            ),
        );
    }
}
