<?php

namespace App\Menu\Factory;

use App\Menu\Type\MenuIconType;
use App\Menu\Type\MenuType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Creates the admin navbar menu.
 */
#[AutoconfigureTag('app.menu_factory')]
class AdminNavbarMenuTypeFactory implements MenuTypeFactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'navbar_admin';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(): MenuType
    {
        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_admin_root');

        $item1 = new MenuIconType('link1', 'navbar_admin_item', 'item 1', '#', 'fa-solid fa-gauge');
        $item2 = new MenuIconType('link2', 'navbar_admin_item', 'item 2', '#', 'fa-solid fa-gauge');
        $item3 = new MenuIconType('link3', 'navbar_admin_item', 'item 3', '#', 'fa-solid fa-gauge');

        $item4 = new MenuIconType('link4', 'navbar_admin_item', 'item 4', '#',  'far fa-circle');
        $item4->setParent($item1);

        $item5 = new MenuIconType('link5', 'navbar_admin_item', 'item 5', '#',  'far fa-circle');
        $item5->setParent($item1);
        $item5->setActive(true, true);

        $menu
            ->addChild($item1)
            ->addChild($item2)
            ->addChild($item3)
        ;

        return $menu;
    }
}