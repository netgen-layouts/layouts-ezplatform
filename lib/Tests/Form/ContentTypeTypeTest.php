<?php

namespace Netgen\BlockManager\Ez\Tests\Form;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeTypeTest extends FormTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeServiceMock;

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

        $submittedData = array('article', 'news');

        $form = $this->factory->create(
            ContentTypeType::class,
            null,
            array('multiple' => true)
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

        $options = $optionsResolver->resolve(array());

        $this->assertTrue($options['choices_as_values']);
        $this->assertFalse($options['choice_translation_domain']);
        $this->assertEquals(
            array(
                'Group' => array(
                    'Article' => 'article',
                    'News' => 'news',
                ),
            ),
            $options['choices']
        );
    }

    protected function configureContentTypeService()
    {
        $contentTypeGroup = new ContentTypeGroup(array('identifier' => 'Group'));

        $this->contentTypeServiceMock
            ->expects($this->at(0))
            ->method('loadContentTypeGroups')
            ->will($this->returnValue(array($contentTypeGroup)));

        $this->contentTypeServiceMock
            ->expects($this->at(1))
            ->method('loadContentTypes')
            ->with($this->equalTo($contentTypeGroup))
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
    }
}
