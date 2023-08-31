<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use App\Model\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the main user website menu.
 */
class UserNavbarMenuTypeFactory extends AbstractMenuTypeFactory
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
        return 'navbar_user';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->get('_route', '');

        // root
        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_user');

        // home
        $text = $this->translator->trans('route.user_home');
        $url = $this->urlGenerator->generate('user_home');
        $itemHome = new MenuType('user_home', 'navbar_user_item', $text, $url);
        $itemHome->setActive($route === 'user_home');
        $menu->addChild($itemHome);

        // camp catalog
        $text = $this->translator->trans('route.user_camp_catalog');
        $url = $this->urlGenerator->generate('user_camp_catalog');
        $itemCampCatalog = new MenuType('user_camp_catalog', 'navbar_user_item', $text, $url);
        $itemCampCatalog->setActive($route === 'user_camp_catalog');
        $menu->addChild($itemCampCatalog);

        // admin
        if ($this->security->isGranted('_any_permission'))
        {
            $text = $this->translator->trans('module.admin');
            $url = $this->urlGenerator->generate('admin_home');
            $itemAdmin = new MenuType('admin_home', 'navbar_user_item', $text, $url);
            $menu->addChild($itemAdmin);
        }

        // profile, logout
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            /** @var User $user */
            $user = $this->security->getUser();

            // profile - parent
            $text = $user->getEmail();
            $itemProfileParent = new MenuType('parent_profile', 'navbar_user_item', $text, '#');
            $menu->addChild($itemProfileParent);

            // profile - billing
            $active = $route === 'user_profile_billing';

            $text = $this->translator->trans('route.user_profile_billing');
            $url = $this->urlGenerator->generate('user_profile_billing');
            $itemProfileBilling = new MenuType('user_profile_billing', 'navbar_user_dropdown_item', $text, $url);
            $itemProfileParent->addChild($itemProfileBilling);
            $itemProfileBilling->setActive($active, $active);

            // profile - contacts
            $active =
                $route === 'user_profile_contact_list'   || $route === 'user_profile_contact_create' || $route === 'user_profile_contact_read' ||
                $route === 'user_profile_contact_update' || $route === 'user_profile_contact_delete'
            ;

            $text = $this->translator->trans('route.user_profile_contact_list');
            $url = $this->urlGenerator->generate('user_profile_contact_list');
            $itemProfileContacts = new MenuType('user_profile_contact_list', 'navbar_user_dropdown_item', $text, $url);
            $itemProfileParent->addChild($itemProfileContacts);
            $itemProfileContacts->setActive($active, $active);

            // profile - campers
            $active =
                $route === 'user_profile_camper_list'   || $route === 'user_profile_camper_create' || $route === 'user_profile_camper_read' ||
                $route === 'user_profile_camper_update' || $route === 'user_profile_camper_delete'
            ;

            $text = $this->translator->trans('route.user_profile_camper_list');
            $url = $this->urlGenerator->generate('user_profile_camper_list');
            $itemProfileCampers = new MenuType('user_profile_camper_list', 'navbar_user_dropdown_item', $text, $url);
            $itemProfileParent->addChild($itemProfileCampers);
            $itemProfileCampers->setActive($active, $active);

            // profile - applications
            $itemProfileApplications = new MenuType('user_profile_application_list', 'navbar_user_dropdown_item', 'applications', '#');
            $itemProfileParent->addChild($itemProfileApplications);

            // profile - password set
            if ($user->getPassword() === null)
            {
                $active = $route === 'user_profile_password_change_create';

                $text = $this->translator->trans('route.user_profile_password_change_create');
                $url = $this->urlGenerator->generate('user_profile_password_change_create');
                $itemProfilePassword = new MenuType('user_profile_password_change_create', 'navbar_user_dropdown_item', $text, $url);
            }
            // profile - password change
            else
            {
                $active = $route === 'user_profile_password_change';

                $text = $this->translator->trans('route.user_profile_password_change');
                $url = $this->urlGenerator->generate('user_profile_password_change');
                $itemProfilePassword = new MenuType('user_profile_password_change', 'navbar_user_dropdown_item', $text, $url);
            }

            $itemProfileParent->addChild($itemProfilePassword);
            $itemProfilePassword->setActive($active, $active);

            // logout
            $text = $this->translator->trans('route.user_logout');
            $url = $this->urlGenerator->generate('user_logout');
            $itemLogout = new MenuType('user_logout', 'navbar_user_item', $text, $url);
            $menu->addChild($itemLogout);
        }
        // login
        else
        {
            $active =
                $route === 'user_login'           || $route === 'user_registration' || $route === 'user_registration_complete' ||
                $route === 'user_password_change' || $route === 'user_password_change_complete'
            ;

            $text = $this->translator->trans('route.user_login');
            $url = $this->urlGenerator->generate('user_login');
            $itemLogin = new MenuType('user_login', 'navbar_user_item', $text, $url);
            $itemLogin->setActive($active);
            $menu->addChild($itemLogin);
        }

        return $menu;
    }
}