<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Model\Entity\Camp;
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
            $this->addChildRoute($root, 'user_camp_catalog', ['path' => $path], 'user_camp_catalog_' . $key)
                ->setText($text)
                ->setActive($key === array_key_last($campCategories))
            ;
        }

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDetail(Camp $camp): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_camp_catalog');

        $campCategory = $camp->getCampCategory();
        $campCategories = [];

        if ($campCategory !== null)
        {
            $campCategories = $campCategory->getAncestors();
            $campCategories[] = $campCategory;
        }

        foreach ($campCategories as $key => $campCategory)
        {
            $path = $campCategory->getPath();
            $text = $campCategory->getName();

            $this->addChildRoute($root, 'user_camp_catalog', ['path' => $path], 'user_camp_catalog_' . $key)
                ->setText($text)
            ;
        }

        $text = $camp->getName();
        $this->addChildRoute($root, 'user_camp_detail', ['urlName' => $camp->getUrlName()])
            ->setText($text)
            ->setActive()
        ;

        return $root;
    }
}