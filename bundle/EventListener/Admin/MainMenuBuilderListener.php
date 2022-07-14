<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\EventListener\Admin;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function array_keys;
use function array_search;
use function array_splice;
use function is_int;

final class MainMenuBuilderListener implements EventSubscriberInterface
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getSubscribedEvents(): array
    {
        return [ConfigureMenuEvent::MAIN_MENU => 'onMainMenuBuild'];
    }

    /**
     * This method adds Netgen Layouts menu items to eZ Platform admin interface.
     */
    public function onMainMenuBuild(ConfigureMenuEvent $event): void
    {
        if (!$this->authorizationChecker->isGranted('nglayouts:ui:access')) {
            return;
        }

        $this->addLayoutsSubMenu($event->getMenu());
    }

    /**
     * Adds the Netgen Layouts submenu to eZ Platform admin interface.
     */
    private function addLayoutsSubMenu(ItemInterface $menu): void
    {
        $menuOrder = $this->getNewMenuOrder($menu);

        $layouts = $menu
            ->addChild('nglayouts')
            ->setLabel('menu.main_menu.header')
            ->setExtra('translation_domain', 'nglayouts_admin');

        $layouts
            ->addChild('layout_resolver', ['route' => 'nglayouts_admin_layout_resolver_index'])
            ->setLabel('menu.main_menu.layout_resolver')
            ->setExtra('translation_domain', 'nglayouts_admin');

        $layouts
            ->addChild('layouts', ['route' => 'nglayouts_admin_layouts_index'])
            ->setLabel('menu.main_menu.layouts')
            ->setExtra('translation_domain', 'nglayouts_admin');

        $layouts
            ->addChild('shared_layouts', ['route' => 'nglayouts_admin_shared_layouts_index'])
            ->setLabel('menu.main_menu.shared_layouts')
            ->setExtra('translation_domain', 'nglayouts_admin');

        $layouts
            ->addChild('transfer', ['route' => 'nglayouts_admin_transfer_index'])
            ->setLabel('menu.main_menu.transfer')
            ->setExtra('translation_domain', 'nglayouts_admin');

        $menu->reorderChildren($menuOrder);
    }

    /**
     * Returns the new menu order.
     *
     * @return string[]
     */
    private function getNewMenuOrder(ItemInterface $menu): array
    {
        $menuOrder = array_keys($menu->getChildren());
        $configMenuIndex = array_search(MainMenuBuilder::ITEM_ADMIN, $menuOrder, true);
        if (is_int($configMenuIndex)) {
            array_splice($menuOrder, $configMenuIndex, 0, ['nglayouts']);

            return $menuOrder;
        }

        $menuOrder[] = 'nglayouts';

        return $menuOrder;
    }
}
