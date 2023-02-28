<?php

namespace App\Search\Menu;

use App\Menu\Type\MenuTypeInterface;

/**
 * Service used for searching menu type trees.
 */
interface MenuSearchInterface
{
    /**
     * Sorts all child nodes using their priority attribute in descending order.
     *
     * @param MenuTypeInterface $menuType
     * @return void
     */
    public function sortChildrenRecursively(MenuTypeInterface $menuType): void;
}