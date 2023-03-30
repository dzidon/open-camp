<?php

namespace App\Tests\Functional\Menu;

use App\Menu\Type\MenuTypeInterface;

/**
 * Trait that helps to work with menu type children identifiers.
 */
trait MenuTypeChildrenIdentifiersTrait
{
    /**
     * Returns an array containing identifiers of children of a given menu type.
     *
     * @param MenuTypeInterface $menuType
     * @return array
     */
    private function getMenuTypeChildrenIdentifiers(MenuTypeInterface $menuType): array
    {
        $identifiers = [];
        foreach ($menuType->getChildren() as $child)
        {
            $identifiers[] = $child->getIdentifier();
        }

        return $identifiers;
    }
}