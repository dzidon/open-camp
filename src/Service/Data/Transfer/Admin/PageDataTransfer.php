<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\PageData;
use App\Model\Entity\Page;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link PageData} to {@link Page} and vice versa.
 */
class PageDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof PageData && $entity instanceof Page;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var PageData $pageData */
        /** @var Page $page */
        $pageData = $data;
        $page = $entity;

        $pageData->setTitle($page->getTitle());
        $pageData->setUrlName($page->getUrlName());
        $pageData->setContent($page->getContent());
        $pageData->setIsHidden($page->isHidden());

        $menuPriority = $page->getMenuPriority('navbar_user');

        if ($menuPriority === null)
        {
            $pageData->setIsInMenu(false);
            $pageData->setMenuPriority(null);
        }
        else
        {
            $pageData->setIsInMenu(true);
            $pageData->setMenuPriority($menuPriority);
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var PageData $pageData */
        /** @var Page $page */
        $pageData = $data;
        $page = $entity;

        $page->setTitle($pageData->getTitle());
        $page->setUrlName($pageData->getUrlName());
        $page->setContent($pageData->getContent());
        $page->setIsHidden($pageData->isHidden());

        if ($pageData->isInMenu())
        {
            $page->setMenuPriority('navbar_user', $pageData->getMenuPriority());
        }
        else
        {
            $page->removeMenuPriority('navbar_user');
        }
    }
}