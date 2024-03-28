<?php

namespace App\Service\Menu\Breadcrumbs\Admin\ApplicationContact;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\ApplicationContact;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_application_contact_update';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_application_contact_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var ApplicationContact $applicationContact */
        $applicationContact = $options['application_contact'];

        $this->addRoute($breadcrumbs, 'admin_application_contact_update', [
            'id' => $applicationContact->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('application_contact');
        $resolver->setAllowedTypes('application_contact', ApplicationContact::class);
        $resolver->setRequired('application_contact');
    }
}