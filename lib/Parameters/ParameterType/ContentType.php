<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\ParameterType;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Netgen\Layouts\Ez\Validator\Constraint as EzConstraints;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an ID of a content in eZ Platform.
 */
final class ContentType extends ParameterType
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public static function getIdentifier(): string
    {
        return 'ezcontent';
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired(['allow_invalid', 'allowed_types']);

        $optionsResolver->setDefault('allow_invalid', false);
        $optionsResolver->setDefault('allowed_types', []);

        $optionsResolver->setAllowedTypes('allow_invalid', 'bool');
        $optionsResolver->setAllowedTypes('allowed_types', 'string[]');
    }

    public function export(ParameterDefinition $parameterDefinition, $value)
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
            $contentInfo = $this->repository->sudo(
                static function (Repository $repository) use ($value): ContentInfo {
                    return $repository->getContentService()->loadContentInfo((int) $value);
                }
            );

            return $contentInfo->remoteId;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function import(ParameterDefinition $parameterDefinition, $value)
    {
        try {
            /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
            $contentInfo = $this->repository->sudo(
                static function (Repository $repository) use ($value): ContentInfo {
                    return $repository->getContentService()->loadContentInfoByRemoteId((string) $value);
                }
            );

            return $contentInfo->id;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        $options = $parameterDefinition->getOptions();

        return [
            new Constraints\Type(['type' => 'numeric']),
            new Constraints\GreaterThan(['value' => 0]),
            new EzConstraints\Content(
                [
                    'allowInvalid' => $options['allow_invalid'],
                    'allowedTypes' => $options['allowed_types'],
                ]
            ),
        ];
    }
}
