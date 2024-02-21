<?php

namespace App\Service\Menu\Breadcrumbs\User\Registration;

use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationCompleteBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_registration_complete';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_registration';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var string $token */
        $token = $options['token'];

        $this->addRoute($breadcrumbs, 'user_registration_complete', ['token' => $token]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('token');
        $resolver->setAllowedTypes('token', 'string');
        $resolver->setRequired('token');
    }
}