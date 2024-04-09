<?php

namespace App\Service\Menu\Breadcrumbs\User\Guide;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\User;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetailBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_guide_detail';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_guide_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var User $guide */
        $guide = $options['guide'];

        $guideLink = $this->addRoute($breadcrumbs, 'user_guide_detail', ['urlName' => $guide->getUrlName()]);
        $nameFull = $guide->getNameFull();

        if ($nameFull === null)
        {
            $guideText = $this->translator->trans('guide.unnamed');
        }
        else
        {
            $guideText = $guideLink->getText();
            $guideText = sprintf('%s %s', $guideText, $nameFull);
        }

        $guideLink->setText($guideText);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('guide');
        $resolver->setAllowedTypes('guide', User::class);
        $resolver->setRequired('guide');
    }
}