<?php

namespace App\Service\Menu\Breadcrumbs\Admin\Application;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Application;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_application_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_application_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var Application $application */
        $application = $options['application'];

        $this->addRoute($breadcrumbs, 'admin_application_read', [
            'id' => $application->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('application');
        $resolver->setAllowedTypes('application', Application::class);
        $resolver->setRequired('application');
    }
}