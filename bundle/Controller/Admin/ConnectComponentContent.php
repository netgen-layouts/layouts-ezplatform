<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsIbexaBundle\Controller\Admin;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Ibexa\Block\BlockDefinition\Handler\ComponentHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ConnectComponentContent extends Controller
{
    public function __construct(private BlockService $blockService, private ContentService $contentService) {}

    /**
     * Connects the provided content with the provided component block.
     */
    public function __invoke(Request $request, Block $block, int $contentId): Response
    {
        if (!$block->getDefinition()->getHandler() instanceof ComponentHandler) {
            throw new BadRequestHttpException();
        }

        try {
            $content = $this->contentService->loadContent($contentId);
        } catch (NotFoundException|UnauthorizedException) {
            throw new BadRequestHttpException();
        }

        $blockUpdateStruct = $this->blockService->newBlockUpdateStruct($block->getLocale());
        $blockUpdateStruct->setParameterValue('content', $content->id);

        $this->blockService->updateBlock($block, $blockUpdateStruct);

        return new Response();
    }

    public function checkPermissions(): void
    {
        if ($this->isGranted('ROLE_NGLAYOUTS_EDITOR')) {
            return;
        }

        if ($this->isGranted('nglayouts:ui:access')) {
            return;
        }

        $exception = $this->createAccessDeniedException();
        $exception->setAttributes('nglayouts:ui:access');

        throw $exception;
    }
}
