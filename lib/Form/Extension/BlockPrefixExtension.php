<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Form\Extension;

use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BlockPrefixExtension extends AbstractTypeExtension
{
    public function getExtendedType(): string
    {
        return ContentBrowserType::class;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ContentBrowserType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('block_prefix', null);
        $resolver->setAllowedTypes('block_prefix', ['null', 'string']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($options['block_prefix'] !== null) {
            $view->vars['block_prefixes'][] = $options['block_prefix'];
        }
    }
}
