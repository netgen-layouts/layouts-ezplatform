<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Form;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use Netgen\Layouts\Ez\Form\ContentTypeType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentTypeTypeTest extends FormTestCase
{
    private MockObject $contentTypeServiceMock;

    /**
     * @covers \Netgen\Layouts\Ez\Form\ContentTypeType::__construct
     * @covers \Netgen\Layouts\Ez\Form\ContentTypeType::getContentTypes
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
            ],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($submittedData, $form->getData());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\ContentTypeType::getParent
     */
    public function testGetParent(): void
    {
        self::assertSame(ChoiceType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\ContentTypeType::configureOptions
     * @covers \Netgen\Layouts\Ez\Form\ContentTypeType::getContentTypes
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
            ],
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
            $options['choices'],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        return new ContentTypeType(
            $this->contentTypeServiceMock,
        );
    }

    private function configureContentTypeService(): void
    {
        $contentTypeGroup1 = new ContentTypeGroup(['identifier' => 'Group1']);
        $contentTypeGroup2 = new ContentTypeGroup(['identifier' => 'Group2']);
        $contentTypeGroup3 = new ContentTypeGroup(['identifier' => 'Group3']);

        $this->contentTypeServiceMock
            ->method('loadContentTypeGroups')
            ->willReturn([$contentTypeGroup1, $contentTypeGroup2, $contentTypeGroup3]);

        $this->contentTypeServiceMock
            ->method('loadContentTypes')
            ->willReturnMap(
                [
                    [
                        $contentTypeGroup1,
                        [],
                        [
                            new ContentType(
                                [
                                    'identifier' => 'article',
                                    'names' => ['eng-GB' => 'Article'],
                                    'mainLanguageCode' => 'eng-GB',
                                ],
                            ),
                            new ContentType(
                                [
                                    'identifier' => 'news',
                                    'names' => ['eng-GB' => 'News'],
                                    'mainLanguageCode' => 'eng-GB',
                                ],
                            ),
                        ],
                    ],
                    [
                        $contentTypeGroup2,
                        [],
                        [
                            new ContentType(
                                [
                                    'identifier' => 'image',
                                    'names' => ['eng-GB' => 'Image'],
                                    'mainLanguageCode' => 'eng-GB',
                                ],
                            ),
                        ],
                    ],
                ],
            );
    }
}
