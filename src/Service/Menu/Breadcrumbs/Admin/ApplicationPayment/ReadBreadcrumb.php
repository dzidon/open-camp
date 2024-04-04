<?php

namespace App\Service\Menu\Breadcrumbs\Admin\ApplicationPayment;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\ApplicationPayment;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_application_payment_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_application_payment_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var ApplicationPayment $ApplicationPayment */
        $ApplicationPayment = $options['application_payment'];

        $this->addRoute($breadcrumbs, 'admin_application_payment_read', [
            'id' => $ApplicationPayment->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('application_payment');
        $resolver->setAllowedTypes('application_payment', ApplicationPayment::class);
        $resolver->setRequired('application_payment');
    }
}