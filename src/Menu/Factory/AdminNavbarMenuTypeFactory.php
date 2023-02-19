<?php

namespace App\Menu\Factory;

use App\Menu\Type\MenuIconType;
use App\Menu\Type\MenuType;
use App\Menu\Type\MenuTypeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Class used for creating the admin navbar menu.
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
    public function buildMenuType(): MenuTypeInterface
    {
        $menu = new MenuType(self::getMenuIdentifier(), 'admin_navbar_root');

        $item1 = new MenuIconType('link1', 'admin_navbar', 'item 1', '#', 'fa-solid fa-gauge');
        $item2 = new MenuIconType('link2', 'admin_navbar', 'item 2', '#', 'fa-solid fa-gauge');
        $item3 = new MenuIconType('link3', 'admin_navbar', 'item 3', '#', 'fa-solid fa-gauge');

        $item4 = new MenuIconType('link4', 'admin_navbar', 'item 4', '#',  'far fa-circle');
        $item4->setParent($item1);

        $item5 = new MenuIconType('link5', 'admin_navbar', 'item 5', '#',  'far fa-circle');
        $item5->setParent($item1);
        $item5->setActive();

        $menu
            ->addChild($item1)
            ->addChild($item2)
            ->addChild($item3)
        ;

        return $menu;
    }
}