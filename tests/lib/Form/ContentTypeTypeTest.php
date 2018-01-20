<?php

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

        $submittedData = array('article');

        $form = $this->factory->create(
            ContentTypeType::class,
            null,
            array(
                'multiple' => true,
                'types' => array(
                    'Group1' => array('article'),
                    'Group3' => false,
                ),
            )
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
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::getContentTypes
     * @covers \Netgen\BlockManager\Ez\Form\ContentTypeType::configureOptions
     */
    public function testConfigureOptions()
    {
        $this->configureContentTypeService();

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'types' => array(
                    'Group1' => array('article'),
                    'Group3' => false,
                ),
            )
        );

        $this->assertFalse($options['choice_translation_domain']);
        $this->assertEquals(
            array(
                'Group1' => array(
                    'Article' => 'article',
                ),
                'Group2' => array(
                    'Image' => 'image',
                ),
            ),
            $options['choices']
        );

        if (Kernel::VERSION_ID < 30100) {
            // @deprecated Remove when support for Symfony 2.8 ends
            $this->assertTrue($options['choices_as_values']);
        }
    }

    private function configureContentTypeService()
    {
        $contentTypeGroup1 = new ContentTypeGroup(array('identifier' => 'Group1'));
        $contentTypeGroup2 = new ContentTypeGroup(array('identifier' => 'Group2'));
        $contentTypeGroup3 = new ContentTypeGroup(array('identifier' => 'Group3'));

        $this->contentTypeServiceMock
            ->expects($this->at(0))
            ->method('loadContentTypeGroups')
            ->will($this->returnValue(array($contentTypeGroup1, $contentTypeGroup2, $contentTypeGroup3)));

        $this->contentTypeServiceMock
            ->expects($this->at(1))
            ->method('loadContentTypes')
            ->with($this->equalTo($contentTypeGroup1))
            ->will(
                $this->returnValue(
                    array(
                        new ContentType(
                            array(
                                'identifier' => 'article',
                                'names' => array(
                                    'eng-GB' => 'Article',
                                ),
                                'fieldDefinitions' => array(),
                            )
                        ),
                        new ContentType(
                            array(
                                'identifier' => 'news',
                                'names' => array(
                                    'eng-GB' => 'News',
                                ),
                                'fieldDefinitions' => array(),
                            )
                        ),
                    )
                )
            );

        $this->contentTypeServiceMock
            ->expects($this->at(2))
            ->method('loadContentTypes')
            ->with($this->equalTo($contentTypeGroup2))
            ->will(
                $this->returnValue(
                    array(
                        new ContentType(
                            array(
                                'identifier' => 'image',
                                'names' => array(
                                    'eng-GB' => 'Image',
                                ),
                                'fieldDefinitions' => array(),
                            )
                        ),
                    )
                )
            );
    }
}
