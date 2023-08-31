<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Model\Entity\CampCategory;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;
use LogicException;

/**
 * @inheritDoc
 */
class CampCatalogBreadcrumbs extends AbstractBreadcrumbs implements CampCatalogBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(array $campCategories): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $catalogChild = $this->addChildRoute($root, 'user_camp_catalog');

        if (empty($campCategories))
        {
            $catalogChild->setActive();
        }

        foreach ($campCategories as $key => $campCategory)
        {
            if (!$campCategory instanceof CampCategory)
            {
                throw new LogicException(
                    sprintf('Camp categories passed to "%s" can only contain instances of "%s".', __METHOD__, CampCategory::class)
                );
            }

            $path = $campCategory->getPath();
            $text = $campCategory->getName();
            $child = $this->addChildRoute($root, 'user_camp_catalog', ['path' => $path], 'user_camp_catalog_' . $key);
            $child->setText($text);
            $child->setActive($key === array_key_last($campCategories));
        }

        return $root;
    }
}