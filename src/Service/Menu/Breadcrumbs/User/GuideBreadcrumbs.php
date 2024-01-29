<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Model\Entity\User;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class GuideBreadcrumbs extends AbstractBreadcrumbs implements GuideBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildDetail(User $guide): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $guideRoot = $this->addChildRoute($root, 'user_guide_detail', ['urlName' => $guide->getUrlName()])
            ->setActive()
        ;

        $nameFull = $guide->getNameFull();

        if ($nameFull === null)
        {
            $guideText = $this->translator->trans('guide.unnamed');
        }
        else
        {
            $guideText = $guideRoot->getText();
            $guideText = sprintf('%s %s', $guideText, $nameFull);
        }

        $guideRoot->setText($guideText);

        return $root;
    }
}