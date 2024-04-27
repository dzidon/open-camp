<?php

namespace App\Service\Menu\Breadcrumbs\User\Page;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Page;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_page_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_home';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var Page $page */
        $page = $options['page'];
        $title = $page->getTitle();
        $id = $page->getId();

        $this->addRoute($breadcrumbs, 'admin_page_read', ['id' => $id])
            ->setText($title)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('page');
        $resolver->setAllowedTypes('page', Page::class);
        $resolver->setRequired('page');
    }
}