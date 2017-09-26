<?php

namespace Netgen\BlockManager\Ez\Locale;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * eZ Platform specific locale provider that provides the existing locales
 * by reading them from eZ Platform database.
 *
 * @final
 */
class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LanguageService
     */
    private $languageService;

    /**
     * @var \eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface
     */
    private $localeConverter;

    /**
     * @var \Symfony\Component\Intl\ResourceBundle\LocaleBundleInterface
     */
    private $localeBundle;

    /**
     * @var string[]
     */
    private $languageCodes = array();

    public function __construct(LanguageService $languageService, LocaleConverterInterface $localeConverter)
    {
        $this->languageService = $languageService;
        $this->localeConverter = $localeConverter;
        $this->localeBundle = Intl::getLocaleBundle();
    }

    /**
     * Sets the available language codes to the provider.
     *
     * @param string[] $languageCodes
     */
    public function setLanguages(array $languageCodes = null)
    {
        $this->languageCodes = !empty($languageCodes) ? $languageCodes : array();
    }

    public function getAvailableLocales()
    {
        $availableLocales = array();
        $languages = $this->languageService->loadLanguages();

        foreach ($languages as $language) {
            if (!$language->enabled) {
                continue;
            }

            $posixLocale = $this->localeConverter->convertToPOSIX($language->languageCode);
            if ($posixLocale === null) {
                continue;
            }

            $localeName = $this->localeBundle->getLocaleName($posixLocale);

            if ($localeName !== null) {
                $availableLocales[$posixLocale] = $localeName;
            }
        }

        asort($availableLocales);

        return $availableLocales;
    }

    public function getRequestLocales(Request $request)
    {
        $requestLocales = array();

        foreach ($this->languageCodes as $languageCode) {
            try {
                $language = $this->languageService->loadLanguage($languageCode);
            } catch (NotFoundException $e) {
                continue;
            }

            if (!$language->enabled) {
                continue;
            }

            $posixLocale = $this->localeConverter->convertToPOSIX($language->languageCode);
            if ($posixLocale === null) {
                continue;
            }

            $localeName = $this->localeBundle->getLocaleName($posixLocale);

            if ($localeName !== null) {
                $requestLocales[] = $posixLocale;
            }
        }

        return $requestLocales;
    }
}
