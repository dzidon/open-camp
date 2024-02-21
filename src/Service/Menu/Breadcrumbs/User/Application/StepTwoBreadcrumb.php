<?php

namespace App\Service\Menu\Breadcrumbs\User\Application;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Application;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StepTwoBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_application_step_two';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_camp_detail';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var Application $application */
        $application = $options['application'];

        $applicationId = $application->getId();
        $text = $this->translator->trans('entity.application.singular');
        $this->addRoute($breadcrumbs, 'user_application_step_two', ['applicationId' => $applicationId])
            ->setText($text)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('application');
        $resolver->setAllowedTypes('application', Application::class);
        $resolver->setRequired('application');
    }
}