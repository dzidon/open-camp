<?php

namespace App\Service\Menu\Breadcrumbs\Admin\FormField;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\FormField;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_form_field_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_form_field_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var FormField $formField */
        $formField = $options['form_field'];

        $this->addRoute($breadcrumbs, 'admin_form_field_read', [
            'id' => $formField->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('form_field');
        $resolver->setAllowedTypes('form_field', FormField::class);
        $resolver->setRequired('form_field');
    }
}