<?php

namespace App\Service\Menu\Breadcrumbs\Admin\Page;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Page;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_page_delete';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_page_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var Page $page */
        $page = $options['page'];

        $this->addRoute($breadcrumbs, 'admin_page_delete', [
            'id' => $page->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('page');
        $resolver->setAllowedTypes('page', Page::class);
        $resolver->setRequired('page');
    }
}