<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Templating\Twig\Extension;

use eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class EzPlatformBCExtension extends AbstractExtension
{
    /**
     * @var \eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension
     */
    private $contentExtension;

    public function __construct(ContentExtension $contentExtension)
    {
        $this->contentExtension = $contentExtension;
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'nglayouts_ez_field_is_empty',
                [$this->contentExtension, 'isFieldEmpty']
            ),
        ];
    }
}
