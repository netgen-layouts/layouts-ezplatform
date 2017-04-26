<?php

namespace Netgen\BlockManager\Ez\Tests\Locale;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\MVC\Symfony\Locale\LocaleConverterInterface;
use Netgen\BlockManager\Ez\Locale\LocaleProvider;
use PHPUnit\Framework\TestCase;

class LocaleProviderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $languageServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeConverterMock;

    /**
     * @var \Netgen\BlockManager\Ez\Locale\LocaleProvider
     */
    protected $localeProvider;

    public function setUp()
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
     */
    public function testGetAvailableLocales()
    {
        $this->languageServiceMock
            ->expects($this->any())
            ->method('loadLanguages')
            ->will(
                $this->returnValue(
                    array(
                        new Language(array('languageCode' => 'eng-GB', 'enabled' => true)),
                        new Language(array('languageCode' => 'ger-DE', 'enabled' => false)),
                        new Language(array('languageCode' => 'cro-HR', 'enabled' => true)),
                    )
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

        $this->assertEquals(
            array(
                'en' => 'English',
                'hr' => 'Croatian',
            ),
            $this->localeProvider->getAvailableLocales()
        );
    }
}
