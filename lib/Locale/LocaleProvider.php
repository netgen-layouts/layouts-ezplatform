<?php

namespace Netgen\BlockManager\Ez\Locale;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @var \Symfony\Component\Intl\ResourceBundle\LocaleBundleInterface
     */
    protected $localeBundle;

    /**
     * @var string[]
     */
    protected $languageCodes = array();

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
        $languages = $this->languageService->loadLanguages();

        foreach ($languages as $language) {
            if (!$language->enabled) {
                continue;
            }

            $posixLocale = $this->localeConverter->convertToPOSIX($language->languageCode);
            $localeName = $this->localeBundle->getLocaleName($posixLocale);

            if ($localeName !== null) {
                $availableLocales[$posixLocale] = $localeName;
            }
        }

        asort($availableLocales);

        return $availableLocales;
    }

    /**
     * Returns the list of locale codes available for the provided request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string[]
     */
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
            $localeName = $this->localeBundle->getLocaleName($posixLocale);

            if ($localeName !== null) {
                $requestLocales[] = $posixLocale;
            }
        }

        return $requestLocales;
    }
}
