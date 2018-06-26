<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Locale;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
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
    private $languageCodes = [];

    public function __construct(LanguageService $languageService, LocaleConverterInterface $localeConverter)
    {
        $this->languageService = $languageService;
        $this->localeConverter = $localeConverter;
        $this->localeBundle = Intl::getLocaleBundle();
    }

    /**
     * Sets the available language codes to the provider.
     *
     * @param string[]|null $languageCodes
     */
    public function setLanguages(?array $languageCodes = null): void
    {
        $this->languageCodes = $languageCodes ?? [];
    }

    public function getAvailableLocales(): array
    {
        $availableLocales = [];
        $languages = $this->languageService->loadLanguages();

        foreach ($languages as $language) {
            $locale = $this->getPosixLocale($language);

            if (!is_array($locale)) {
                continue;
            }

            $availableLocales[$locale[0]] = $locale[1];
        }

        asort($availableLocales);

        return $availableLocales;
    }

    public function getRequestLocales(Request $request): array
    {
        $requestLocales = [];

        foreach ($this->languageCodes as $languageCode) {
            try {
                $language = $this->languageService->loadLanguage($languageCode);
            } catch (NotFoundException $e) {
                continue;
            }

            $locale = $this->getPosixLocale($language);

            if (!is_array($locale)) {
                continue;
            }

            $requestLocales[] = $locale[0];
        }

        return $requestLocales;
    }

    /**
     * Returns the array with POSIX locale code and name for provided eZ Platform language.
     *
     * If POSIX locale does not exist or if language is not enabled, null will be returned.
     */
    private function getPosixLocale(Language $language): ?array
    {
        if (!$language->enabled) {
            return null;
        }

        $posixLocale = $this->localeConverter->convertToPOSIX($language->languageCode);
        if ($posixLocale === null) {
            return null;
        }

        $localeName = $this->localeBundle->getLocaleName($posixLocale);

        if ($localeName === null) {
            return null;
        }

        return [$posixLocale, $localeName];
    }
}
