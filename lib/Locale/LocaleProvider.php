<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Locale;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Netgen\Layouts\Locale\LocaleProviderInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\Locales;
use Symfony\Component\HttpFoundation\Request;

use function asort;
use function is_array;

/**
 * eZ Platform specific locale provider that provides the existing locales
 * by reading them from eZ Platform database.
 */
final class LocaleProvider implements LocaleProviderInterface
{
    private LanguageService $languageService;

    private LocaleConverterInterface $localeConverter;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        LanguageService $languageService,
        LocaleConverterInterface $localeConverter,
        ConfigResolverInterface $configResolver
    ) {
        $this->languageService = $languageService;
        $this->localeConverter = $localeConverter;
        $this->configResolver = $configResolver;
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
        $languages = $this->languageService->loadLanguageListByCode(
            $this->configResolver->getParameter('languages'),
        );

        foreach ($languages as $language) {
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
     *
     * @return string[]|null
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

        if (!Locales::exists($posixLocale)) {
            return null;
        }

        return [$posixLocale, Locales::getName($posixLocale)];
    }
}
