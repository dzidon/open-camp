<?php

namespace App\Service\Menu\Breadcrumbs\User\Payment;

use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_application_payment_online_status';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_application_completed';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var array $getParameters */
        $getParameters = $options['get_parameters'];

        $this->addRoute($breadcrumbs, 'user_application_payment_online_status', $getParameters);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('get_parameters');
        $resolver->setAllowedTypes('get_parameters', 'array');
        $resolver->setRequired('get_parameters');
    }
}