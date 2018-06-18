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

final class LocaleProviderTest extends TestCase
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
            ->expects($this->any())
            ->method('loadLanguages')
            ->will(
                $this->returnValue(
                    [
                        new Language(['languageCode' => 'eng-GB', 'enabled' => true]),
                        new Language(['languageCode' => 'ger-DE', 'enabled' => false]),
                        new Language(['languageCode' => 'cro-HR', 'enabled' => true]),
                    ]
                )
            );

        $this->localeConverterMock
            ->expects($this->at(0))
            ->method('convertToPOSIX')
            ->with($this->equalTo('eng-GB'))
            ->will($this->returnValue('en'));

        $this->localeConverterMock
            ->expects($this->at(1))
            ->method('convertToPOSIX')
            ->with($this->equalTo('cro-HR'))
            ->will($this->returnValue('hr'));

        $availableLocales = $this->localeProvider->getAvailableLocales();

        $this->assertSame(['hr', 'en'], array_keys($availableLocales));
        $this->assertSame(['Croatian', 'English'], array_values($availableLocales));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::__construct
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getAvailableLocales
     * @covers \Netgen\BlockManager\Ez\Locale\LocaleProvider::getPosixLocale
     */
    public function testGetAvailableLocalesWithInvalidPosixLocale(): void
    {
        $this->languageServiceMock
            ->expects($this->any())
            ->method('loadLanguages')
            ->will(
                $this->returnValue(
                    [
                        new Language(['languageCode' => 'unknown', 'enabled' => true]),
                    ]
                )
            );

        $this->localeConverterMock
            ->expects($this->at(0))
            ->method('convertToPOSIX')
            ->with($this->equalTo('unknown'))
            ->will($this->returnValue(null));

        $availableLocales = $this->localeProvider->getAvailableLocales();

        $this->assertSame([], $availableLocales);
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
            ->expects($this->at(0))
            ->method('loadLanguage')
            ->with($this->equalTo('eng-GB'))
            ->will(
                $this->returnValue(
                    new Language(['languageCode' => 'eng-GB', 'enabled' => true])
                )
            );

        $this->languageServiceMock
            ->expects($this->at(1))
            ->method('loadLanguage')
            ->with($this->equalTo('ger-DE'))
            ->will(
                $this->returnValue(
                    new Language(['languageCode' => 'ger-DE', 'enabled' => false])
                )
            );

        $this->languageServiceMock
            ->expects($this->at(2))
            ->method('loadLanguage')
            ->with($this->equalTo('unknown'))
            ->will(
                $this->throwException(
                    new NotFoundException('language', 'unknown')
                )
            );

        $this->languageServiceMock
            ->expects($this->at(3))
            ->method('loadLanguage')
            ->with($this->equalTo('cro-HR'))
            ->will(
                $this->returnValue(
                    new Language(['languageCode' => 'cro-HR', 'enabled' => true])
                )
            );

        $this->localeConverterMock
            ->expects($this->at(0))
            ->method('convertToPOSIX')
            ->with($this->equalTo('eng-GB'))
            ->will($this->returnValue('en'));

        $this->localeConverterMock
            ->expects($this->at(1))
            ->method('convertToPOSIX')
            ->with($this->equalTo('cro-HR'))
            ->will($this->returnValue('hr'));

        $requestLocales = $this->localeProvider->getRequestLocales(Request::create(''));

        $this->assertSame(['en', 'hr'], $requestLocales);
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
            ->expects($this->at(0))
            ->method('loadLanguage')
            ->with($this->equalTo('eng-GB'))
            ->will(
                $this->returnValue(
                    new Language(['languageCode' => 'eng-GB', 'enabled' => true])
                )
            );

        $this->localeConverterMock
            ->expects($this->at(0))
            ->method('convertToPOSIX')
            ->with($this->equalTo('eng-GB'))
            ->will($this->returnValue(null));

        $requestLocales = $this->localeProvider->getRequestLocales(Request::create(''));

        $this->assertSame([], $requestLocales);
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
            ->expects($this->at(0))
            ->method('loadLanguage')
            ->with($this->equalTo('eng-GB'))
            ->will(
                $this->returnValue(
                    new Language(['languageCode' => 'eng-GB', 'enabled' => true])
                )
            );

        $this->localeConverterMock
            ->expects($this->at(0))
            ->method('convertToPOSIX')
            ->with($this->equalTo('eng-GB'))
            ->will($this->returnValue('unknown'));

        $requestLocales = $this->localeProvider->getRequestLocales(Request::create(''));

        $this->assertSame([], $requestLocales);
    }
}
