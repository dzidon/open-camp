<?php

namespace App\Service\Menu\Breadcrumbs\User\Application;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\CampDate;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StepOneCreateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_application_step_one_create';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_camp_detail';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var CampDate $campDate */
        $campDate = $options['camp_date'];

        $campDateId = $campDate->getId();
        $text = $this->translator->trans('entity.application.singular');
        $this->addRoute($breadcrumbs, 'user_application_step_one_create', ['campDateId' => $campDateId])
            ->setText($text)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('camp_date');
        $resolver->setAllowedTypes('camp_date', CampDate::class);
        $resolver->setRequired('camp_date');
    }
}