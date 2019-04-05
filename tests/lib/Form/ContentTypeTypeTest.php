<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Form;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentTypeTypeTest extends FormTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    public function setUp(): void
    {
        if (Kernel::VERSION_ID < 30400) {
            self::markTestSkipped('This test requires eZ Publish kernel 7.4+ to run.');
        }

        parent::setUp();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::__construct
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::getContentTypes
     */
    public function testSubmitValidData(): void
    {
        $this->configureContentTypeService();

        $submittedData = ['article'];

        $form = $this->factory->create(
            ContentTypeType::class,
            null,
            [
                'multiple' => true,
                'types' => [
                    'Group1' => ['article'],
                    'Group3' => false,
                ],
            ]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($submittedData, $form->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::getParent
     */
    public function testGetParent(): void
    {
        self::assertSame(ChoiceType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::configureOptions
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::getContentTypes
     */
    public function testConfigureOptions(): void
    {
        $this->configureContentTypeService();

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'types' => [
                    'Group1' => ['article'],
                    'Group3' => false,
                ],
            ]
        );

        self::assertFalse($options['choice_translation_domain']);
        self::assertSame(
            [
                'Group1' => [
                    'Article' => 'article',
                ],
                'Group2' => [
                    'Image' => 'image',
                ],
            ],
            $options['choices']
        );

        if (Kernel::VERSION_ID < 30100) {
            // @deprecated Remove when support for Symfony 2.8 ends
            self::assertTrue($options['choices_as_values']);
        }
    }

    protected function getMainType(): FormTypeInterface
    {
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        return new ContentTypeType(
            $this->contentTypeServiceMock
        );
    }

    private function configureContentTypeService(): void
    {
        $contentTypeGroup1 = new ContentTypeGroup(['identifier' => 'Group1']);
        $contentTypeGroup2 = new ContentTypeGroup(['identifier' => 'Group2']);
        $contentTypeGroup3 = new ContentTypeGroup(['identifier' => 'Group3']);

        $this->contentTypeServiceMock
            ->expects(self::at(0))
            ->method('loadContentTypeGroups')
            ->willReturn([$contentTypeGroup1, $contentTypeGroup2, $contentTypeGroup3]);

        $this->contentTypeServiceMock
            ->expects(self::at(1))
            ->method('loadContentTypes')
            ->with(self::identicalTo($contentTypeGroup1))
            ->willReturn(
                [
                    new ContentType(
                        [
                            'identifier' => 'article',
                            'names' => ['eng-GB' => 'Article'],
                            'mainLanguageCode' => 'eng-GB',
                        ]
                    ),
                    new ContentType(
                        [
                            'identifier' => 'news',
                            'names' => ['eng-GB' => 'News'],
                            'mainLanguageCode' => 'eng-GB',
                        ]
                    ),
                ]
            );

        $this->contentTypeServiceMock
            ->expects(self::at(2))
            ->method('loadContentTypes')
            ->with(self::identicalTo($contentTypeGroup2))
            ->willReturn(
                [
                    new ContentType(
                        [
                            'identifier' => 'image',
                            'names' => ['eng-GB' => 'Image'],
                            'mainLanguageCode' => 'eng-GB',
                        ]
                    ),
                ]
            );
    }
}
