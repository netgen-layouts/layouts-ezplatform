<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Locale;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Netgen\BlockManager\Ez\Locale\LocaleProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;

final class LocaleProviderLegacyTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $languageServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $localeConverterMock;

    /**
     * @var \Netgen\BlockManager\Ez\Locale\LocaleProvider
     */
    private $localeProvider;

    public function setUp(): void
    {
        if (Kernel::VERSION_ID >= 30000) {
            self::markTestSkipped('This test requires eZ Publish kernel 6.13 to run.');
        }

        $this->languageServiceMock = $this->createMock(LanguageService::class);

        $this->localeConverterMock = $this->createMock(LocaleConverterInterface::class);

        $this->localeProvider = new LocaleProvider(
            $this->languageServiceMock,
            $this->localeConverterMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::__construct
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getAvailableLocales
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getPosixLocale
     */
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
                ]
            );

        $this->localeConverterMock
            ->expects(self::at(0))
            ->method('convertToPOSIX')
            ->with(self::identicalTo('eng-GB'))
            ->willReturn('en');

        $this->localeConverterMock
            ->expects(self::at(1))
            ->method('convertToPOSIX')
            ->with(self::identicalTo('cro-HR'))
            ->willReturn('hr');

        $availableLocales = $this->localeProvider->getAvailableLocales();

        self::assertSame(['hr', 'en'], array_keys($availableLocales));
        self::assertSame(['Croatian', 'English'], array_values($availableLocales));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::__construct
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getAvailableLocales
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getPosixLocale
     */
    public function testGetAvailableLocalesWithInvalidPosixLocale(): void
    {
        $this->languageServiceMock
            ->expects(self::any())
            ->method('loadLanguages')
            ->willReturn(
                [
                    new Language(['languageCode' => 'unknown', 'enabled' => true]),
                ]
            );

        $this->localeConverterMock
            ->expects(self::at(0))
            ->method('convertToPOSIX')
            ->with(self::identicalTo('unknown'))
            ->willReturn(null);

        $availableLocales = $this->localeProvider->getAvailableLocales();

        self::assertSame([], $availableLocales);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getPosixLocale
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getRequestLocales
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::setLanguages
     */
    public function testGetRequestLocales(): void
    {
        $this->localeProvider->setLanguages(['eng-GB', 'ger-DE', 'unknown', 'cro-HR']);

        $this->languageServiceMock
            ->expects(self::at(0))
            ->method('loadLanguage')
            ->with(self::identicalTo('eng-GB'))
            ->willReturn(new Language(['languageCode' => 'eng-GB', 'enabled' => true]));

        $this->languageServiceMock
            ->expects(self::at(1))
            ->method('loadLanguage')
            ->with(self::identicalTo('ger-DE'))
            ->willReturn(new Language(['languageCode' => 'ger-DE', 'enabled' => false]));

        $this->languageServiceMock
            ->expects(self::at(2))
            ->method('loadLanguage')
            ->with(self::identicalTo('unknown'))
            ->willThrowException(new NotFoundException('language', 'unknown'));

        $this->languageServiceMock
            ->expects(self::at(3))
            ->method('loadLanguage')
            ->with(self::identicalTo('cro-HR'))
            ->willReturn(new Language(['languageCode' => 'cro-HR', 'enabled' => true]));

        $this->localeConverterMock
            ->expects(self::at(0))
            ->method('convertToPOSIX')
            ->with(self::identicalTo('eng-GB'))
            ->willReturn('en');

        $this->localeConverterMock
            ->expects(self::at(1))
            ->method('convertToPOSIX')
            ->with(self::identicalTo('cro-HR'))
            ->willReturn('hr');

        $requestLocales = $this->localeProvider->getRequestLocales(Request::create(''));

        self::assertSame(['en', 'hr'], $requestLocales);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getPosixLocale
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getRequestLocales
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::setLanguages
     */
    public function testGetRequestLocalesWithInvalidPosixLocale(): void
    {
        $this->localeProvider->setLanguages(['eng-GB']);

        $this->languageServiceMock
            ->expects(self::at(0))
            ->method('loadLanguage')
            ->with(self::identicalTo('eng-GB'))
            ->willReturn(new Language(['languageCode' => 'eng-GB', 'enabled' => true]));

        $this->localeConverterMock
            ->expects(self::at(0))
            ->method('convertToPOSIX')
            ->with(self::identicalTo('eng-GB'))
            ->willReturn(null);

        $requestLocales = $this->localeProvider->getRequestLocales(Request::create(''));

        self::assertSame([], $requestLocales);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getPosixLocale
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getRequestLocales
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::setLanguages
     */
    public function testGetRequestLocalesWithNonExistingPosixLocale(): void
    {
        $this->localeProvider->setLanguages(['eng-GB']);

        $this->languageServiceMock
            ->expects(self::at(0))
            ->method('loadLanguage')
            ->with(self::identicalTo('eng-GB'))
            ->willReturn(new Language(['languageCode' => 'eng-GB', 'enabled' => true]));

        $this->localeConverterMock
            ->expects(self::at(0))
            ->method('convertToPOSIX')
            ->with(self::identicalTo('eng-GB'))
            ->willReturn('unknown');

        $requestLocales = $this->localeProvider->getRequestLocales(Request::create(''));

        self::assertSame([], $requestLocales);
    }
}
