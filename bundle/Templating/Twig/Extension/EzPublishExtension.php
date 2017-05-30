<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Extension;

use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\Twig\Runtime\EzPublishRuntime;
use Twig_Extension;
use Twig_SimpleFunction;

class EzPublishExtension extends Twig_Extension
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
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'ngbm_ezcontent_name',
                array(EzPublishRuntime::class, 'getContentName')
            ),
            new Twig_SimpleFunction(
                'ngbm_ezlocation_path',
                array(EzPublishRuntime::class, 'getLocationPath')
            ),
            new Twig_SimpleFunction(
                'ngbm_ez_content_type_name',
                array(EzPublishRuntime::class, 'getContentTypeName')
            ),
        );
    }
}
