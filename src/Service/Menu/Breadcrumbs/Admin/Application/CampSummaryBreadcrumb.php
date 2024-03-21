<?php

namespace App\Service\Menu\Breadcrumbs\Admin\Application;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Camp;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampSummaryBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_application_camp_summary';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_application_camp_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var Camp $camp */
        $camp = $options['camp'];

        $this->addRoute($breadcrumbs, 'admin_application_camp_summary', [
            'campId' => $camp->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camp');
        $resolver->setAllowedTypes('camp', Camp::class);
        $resolver->setRequired('camp');
    }
}