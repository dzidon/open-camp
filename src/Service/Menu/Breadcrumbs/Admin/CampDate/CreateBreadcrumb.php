<?php

namespace App\Service\Menu\Breadcrumbs\Admin\CampDate;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Camp;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_camp_date_create';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_camp_date_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var Camp $camp */
        $camp = $options['camp'];

        $this->addRoute($breadcrumbs, 'admin_camp_date_create', [
            'id' => $camp->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camp');
        $resolver->setAllowedTypes('camp', Camp::class);
        $resolver->setRequired('camp');
    }
}