<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EzPublishExtension extends AbstractExtension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return \Twig\TwigFunction[]
     */
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
