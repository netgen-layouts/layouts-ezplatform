<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Locale;

use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Netgen\Layouts\Ibexa\Locale\LocaleProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

use function array_keys;
use function array_values;

#[CoversClass(LocaleProvider::class)]
final class LocaleProviderTest extends TestCase
{
    private MockObject&LanguageService $languageServiceMock;

    private MockObject&LocaleConverterInterface $localeConverterMock;

    private MockObject&ConfigResolverInterface $configResolverMock;

    private LocaleProvider $localeProvider;

    protected function setUp(): void
    {
        $this->languageServiceMock = $this->createMock(LanguageService::class);
        $this->localeConverterMock = $this->createMock(LocaleConverterInterface::class);
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);

        $this->localeProvider = new LocaleProvider(
            $this->languageServiceMock,
            $this->localeConverterMock,
            $this->configResolverMock,
        );
    }

    public function testGetAvailableLocales(): void
    {
        $this->languageServiceMock
            ->expects(self::any())
            ->method('loadLanguages')
            ->willReturn(
                [
                    new Language(['languageCode' => 'eng-GB', 'enabled' => true]),
                    new Language(['languageCode' => 'ger-DE', 'enabled' => false]),
                    new Language(['languageCode' => 'cro-HR', 'enabled' => true]),
                ],
            );

        $this->localeConverterMock
            ->method('convertToPOSIX')
            ->willReturnMap(
                [
                    ['eng-GB', 'en'],
                    ['cro-HR', 'hr'],
                ],
            );

        $availableLocales = $this->localeProvider->getAvailableLocales();

        self::assertSame(['hr', 'en'], array_keys($availableLocales));
        self::assertSame(['Croatian', 'English'], array_values($availableLocales));
    }

    public function testGetAvailableLocalesWithInvalidPosixLocale(): void
    {
        $this->languageServiceMock
            ->expects(self::any())
            ->method('loadLanguages')
            ->willReturn(
                [
                    new Language(['languageCode' => 'unknown', 'enabled' => true]),
                ],
            );

        $this->localeConverterMock
            ->method('convertToPOSIX')
            ->with(self::identicalTo('unknown'))
            ->willReturn(null);

        $availableLocales = $this->localeProvider->getAvailableLocales();

        self::assertSame([], $availableLocales);
    }

    public function testGetRequestLocales(): void
    {
        $this->configResolverMock
            ->method('getParameter')
            ->with(self::identicalTo('languages'))
            ->willReturn(['eng-GB', 'ger-DE', 'unknown', 'cro-HR']);

        $this->languageServiceMock
            ->expects(self::once())
            ->method('loadLanguageListByCode')
            ->with(self::identicalTo(['eng-GB', 'ger-DE', 'unknown', 'cro-HR']))
            ->willReturn(
                [
                    new Language(['languageCode' => 'eng-GB', 'enabled' => true]),
                    new Language(['languageCode' => 'ger-DE', 'enabled' => false]),
                    new Language(['languageCode' => 'cro-HR', 'enabled' => true]),
                ],
            );

        $this->localeConverterMock
            ->method('convertToPOSIX')
            ->willReturnMap(
                [
                    ['eng-GB', 'en'],
                    ['cro-HR', 'hr'],
                ],
            );

        $requestLocales = $this->localeProvider->getRequestLocales(Request::create(''));

        self::assertSame(['en', 'hr'], $requestLocales);
    }

    public function testGetRequestLocalesWithInvalidPosixLocale(): void
    {
        $this->configResolverMock
            ->method('getParameter')
            ->with(self::identicalTo('languages'))
            ->willReturn(['eng-GB']);

        $this->languageServiceMock
            ->expects(self::once())
            ->method('loadLanguageListByCode')
            ->with(self::identicalTo(['eng-GB']))
            ->willReturn(
                [
                    new Language(['languageCode' => 'eng-GB', 'enabled' => true]),
                ],
            );

        $this->localeConverterMock
            ->method('convertToPOSIX')
            ->with(self::identicalTo('eng-GB'))
            ->willReturn(null);

        $requestLocales = $this->localeProvider->getRequestLocales(Request::create(''));

        self::assertSame([], $requestLocales);
    }

    public function testGetRequestLocalesWithNonExistingPosixLocale(): void
    {
        $this->configResolverMock
            ->method('getParameter')
            ->with(self::identicalTo('languages'))
            ->willReturn(['eng-GB']);

        $this->languageServiceMock
            ->expects(self::once())
            ->method('loadLanguageListByCode')
            ->with(self::identicalTo(['eng-GB']))
            ->willReturn(
                [
                    new Language(['languageCode' => 'eng-GB', 'enabled' => true]),
                ],
            );

        $this->localeConverterMock
            ->method('convertToPOSIX')
            ->with(self::identicalTo('eng-GB'))
            ->willReturn('unknown');

        $requestLocales = $this->localeProvider->getRequestLocales(Request::create(''));

        self::assertSame([], $requestLocales);
    }
}
