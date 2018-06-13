<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Form;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentTypeTypeTest extends FormTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        return new ContentTypeType(
            $this->contentTypeServiceMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::__construct
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::getContentTypes
     */
    public function testSubmitValidData()
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

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($submittedData, $form->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::getParent
     */
    public function testGetParent()
    {
        $this->assertEquals(ChoiceType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::configureOptions
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::getContentTypes
     */
    public function testConfigureOptions()
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

        $this->assertFalse($options['choice_translation_domain']);
        $this->assertEquals(
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
            $this->assertTrue($options['choices_as_values']);
        }
    }

    private function configureContentTypeService()
    {
        $contentTypeGroup1 = new ContentTypeGroup(['identifier' => 'Group1']);
        $contentTypeGroup2 = new ContentTypeGroup(['identifier' => 'Group2']);
        $contentTypeGroup3 = new ContentTypeGroup(['identifier' => 'Group3']);

        $this->contentTypeServiceMock
            ->expects($this->at(0))
            ->method('loadContentTypeGroups')
            ->will($this->returnValue([$contentTypeGroup1, $contentTypeGroup2, $contentTypeGroup3]));

        $this->contentTypeServiceMock
            ->expects($this->at(1))
            ->method('loadContentTypes')
            ->with($this->equalTo($contentTypeGroup1))
            ->will(
                $this->returnValue(
                    [
                        new ContentType(
                            [
                                'identifier' => 'article',
                                'names' => [
                                    'eng-GB' => 'Article',
                                ],
                                'fieldDefinitions' => [],
                            ]
                        ),
                        new ContentType(
                            [
                                'identifier' => 'news',
                                'names' => [
                                    'eng-GB' => 'News',
                                ],
                                'fieldDefinitions' => [],
                            ]
                        ),
                    ]
                )
            );

        $this->contentTypeServiceMock
            ->expects($this->at(2))
            ->method('loadContentTypes')
            ->with($this->equalTo($contentTypeGroup2))
            ->will(
                $this->returnValue(
                    [
                        new ContentType(
                            [
                                'identifier' => 'image',
                                'names' => [
                                    'eng-GB' => 'Image',
                                ],
                                'fieldDefinitions' => [],
                            ]
                        ),
                    ]
                )
            );
    }
}
