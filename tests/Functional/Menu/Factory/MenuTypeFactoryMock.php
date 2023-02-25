<?php

namespace App\Tests\Functional\Menu\Factory;

use App\Menu\Factory\MenuTypeFactoryInterface;
use App\Menu\Type\MenuType;
use App\Menu\Type\MenuTypeInterface;

/**
 * Menu type factory used for testing.
 */
class MenuTypeFactoryMock implements MenuTypeFactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'menu_mock';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(): MenuTypeInterface
    {
        return new MenuType(self::getMenuIdentifier(), 'test_root');
    }
}