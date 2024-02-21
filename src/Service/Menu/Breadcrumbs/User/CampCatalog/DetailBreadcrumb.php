<?php

namespace App\Service\Menu\Breadcrumbs\User\CampCatalog;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Camp;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetailBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_camp_detail';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_camp_catalog';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var Camp $camp */
        $camp = $options['camp'];
        $urlName = $camp->getUrlName();
        $text = $camp->getName();

        $this->addRoute($breadcrumbs, 'user_camp_detail', ['urlName' => $urlName])
            ->setText($text)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camp');
        $resolver->setAllowedTypes('camp', Camp::class);
        $resolver->setRequired('camp');
    }
}