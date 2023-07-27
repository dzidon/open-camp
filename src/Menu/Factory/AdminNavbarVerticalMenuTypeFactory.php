<?php

namespace App\Menu\Factory;

use App\Menu\Type\MenuIconType;
use App\Menu\Type\MenuType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the vertical admin navbar menu.
 */
class AdminNavbarVerticalMenuTypeFactory extends AbstractMenuTypeFactory
{
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;
    private RequestStack $requestStack;
    private Security $security;

    public function __construct(TranslatorInterface   $translator,
                                UrlGeneratorInterface $urlGenerator,
                                RequestStack          $requestStack,
                                Security              $security)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'navbar_admin_vertical';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->get('_route', '');

        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_admin_vertical_root');

        // back to user web
        $text = $this->translator->trans('module.user');
        $url = $this->urlGenerator->generate('user_home');
        $itemDashboard = new MenuIconType('user_home', 'navbar_admin_vertical_item', $text, $url, 'fas fa-backward');
        $menu->addChild($itemDashboard);

        // dashboard
        if ($this->security->isGranted('_any_permission'))
        {
            $text = $this->translator->trans('route.admin_home');
            $url = $this->urlGenerator->generate('admin_home');
            $itemDashboard = new MenuIconType('admin_home', 'navbar_admin_vertical_item', $text, $url, 'fa-solid fa-gauge');
            $menu->addChild($itemDashboard);
            $itemDashboard->setActive($route === 'admin_home');
        }

        // camps
        $isGrantedCampCategory =
            $this->security->isGranted('camp_category_create') || $this->security->isGranted('camp_category_read') ||
            $this->security->isGranted('camp_category_update') || $this->security->isGranted('camp_category_delete')
        ;

        if ($isGrantedCampCategory /* || ...*/)
        {
            $text = $this->translator->trans('route.admin_camp_list');
            $itemCampsParent = new MenuIconType('parent_camps', 'navbar_admin_vertical_item', $text, '#', 'fas fa-campground');
            $menu->addChild($itemCampsParent);

            // ...

            if ($isGrantedCampCategory)
            {
                $active =
                    $route === 'admin_camp_category_list'   || $route === 'admin_camp_category_create' || $route === 'admin_camp_category_read' ||
                    $route === 'admin_camp_category_update' || $route === 'admin_camp_category_delete'
                ;

                $text = $this->translator->trans('route.admin_camp_category_list');
                $url = $this->urlGenerator->generate('admin_camp_category_list');
                $itemCampCategories = new MenuIconType('admin_camp_category_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemCampsParent->addChild($itemCampCategories);
                $itemCampCategories->setActive($active, $active);
            }
        }

        // users
        $isGrantedUser =
            $this->security->isGranted('user_create') || $this->security->isGranted('user_read') ||
            $this->security->isGranted('user_update') || $this->security->isGranted('user_delete') ||
            $this->security->isGranted('user_update_role')
        ;

        $isGrantedRole =
            $this->security->isGranted('role_create') || $this->security->isGranted('role_read') ||
            $this->security->isGranted('role_update') || $this->security->isGranted('role_delete')
        ;

        if ($isGrantedUser || $isGrantedRole)
        {
            $text = $this->translator->trans('route.admin_user_list');
            $itemUsersParent = new MenuIconType('parent_users', 'navbar_admin_vertical_item', $text, '#', 'fas fa-user');
            $menu->addChild($itemUsersParent);

            if ($isGrantedUser)
            {
                $active =
                    $route === 'admin_user_list'   || $route === 'admin_user_create'          || $route === 'admin_user_read' ||
                    $route === 'admin_user_update' || $route === 'admin_user_update_password' || $route === 'admin_user_delete'
                ;

                $text = $this->translator->trans('menu.navbar_admin_vertical.browse');
                $url = $this->urlGenerator->generate('admin_user_list');
                $itemUsers = new MenuIconType('admin_user_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemUsersParent->addChild($itemUsers);
                $itemUsers->setActive($active, $active);
            }

            if ($isGrantedRole)
            {
                $active =
                    $route === 'admin_role_list'   || $route === 'admin_role_create' || $route === 'admin_role_read' ||
                    $route === 'admin_role_update' || $route === 'admin_role_delete'
                ;

                $text = $this->translator->trans('route.admin_role_list');
                $url = $this->urlGenerator->generate('admin_role_list');
                $itemRoles = new MenuIconType('admin_role_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemUsersParent->addChild($itemRoles);
                $itemRoles->setActive($active, $active);
            }
        }

        return $menu;
    }
}