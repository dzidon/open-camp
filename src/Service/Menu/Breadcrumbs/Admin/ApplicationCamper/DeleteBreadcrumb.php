<?php

namespace App\Service\Menu\Breadcrumbs\Admin\ApplicationCamper;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\ApplicationCamper;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_application_camper_delete';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_application_camper_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var ApplicationCamper $applicationCamper */
        $applicationCamper = $options['application_camper'];

        $this->addRoute($breadcrumbs, 'admin_application_camper_delete', [
            'id' => $applicationCamper->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('application_camper');
        $resolver->setAllowedTypes('application_camper', ApplicationCamper::class);
        $resolver->setRequired('application_camper');
    }
}