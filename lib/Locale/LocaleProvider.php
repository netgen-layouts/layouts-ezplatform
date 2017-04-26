<?php

namespace Netgen\BlockManager\Ez\Locale;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Symfony\Component\Intl\Intl;

class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LanguageService
     */
    protected $languageService;

    /**
     * @var \eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface
     */
    protected $localeConverter;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface
     */
    public function __construct(LanguageService $languageService, LocaleConverterInterface $localeConverter)
    {
        $this->languageService = $languageService;
        $this->localeConverter = $localeConverter;
    }

    /**
     * Returns the list of locales available in the system.
     *
     * Keys are locale codes and values are locale names.
     *
     * @return string[]
     */
    public function getAvailableLocales()
    {
        $availableLocales = array();
        $localeBundle = Intl::getLocaleBundle();

        $languages = $this->languageService->loadLanguages();

        foreach ($languages as $language) {
            if ($language->enabled) {
                $posixLocale = $this->localeConverter->convertToPOSIX($language->languageCode);
                $localeName = $localeBundle->getLocaleName($posixLocale);

                if ($localeName !== null) {
                    $availableLocales[$posixLocale] = $localeName;
                }
            }
        }

        return $availableLocales;
    }
}
