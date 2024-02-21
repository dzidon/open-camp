<?php

namespace App\Service\Menu\Breadcrumbs\User\Profile\Camper;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Camper;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_profile_camper_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_profile_camper_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var Camper $camper */
        $camper = $options['camper'];

        $this->addRoute($breadcrumbs, 'user_profile_camper_read', [
            'id' => $camper->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camper');
        $resolver->setAllowedTypes('camper', Camper::class);
        $resolver->setRequired('camper');
    }
}