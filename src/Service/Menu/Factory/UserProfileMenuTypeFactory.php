<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use App\Model\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the user profile navbar menu.
 */
class UserProfileMenuTypeFactory extends AbstractMenuTypeFactory
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
        return 'navbar_user_profile';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->get('_route', '');

        // root
        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_user_profile');

        // billing
        $text = $this->translator->trans('route.user_profile_billing');
        $url = $this->urlGenerator->generate('user_profile_billing');
        $itemBilling = new MenuType('user_profile_billing', 'navbar_user_profile_item', $text, $url);
        $menu->addChild($itemBilling);
        $itemBilling->setActive($route === 'user_profile_billing');

        // contacts
        $active =
            $route === 'user_profile_contact_list'   || $route === 'user_profile_contact_create' || $route === 'user_profile_contact_read' ||
            $route === 'user_profile_contact_update' || $route === 'user_profile_contact_delete'
        ;

        $text = $this->translator->trans('route.user_profile_contact_list');
        $url = $this->urlGenerator->generate('user_profile_contact_list');
        $itemContacts = new MenuType('user_profile_contact_list', 'navbar_user_profile_item', $text, $url);
        $menu->addChild($itemContacts);
        $itemContacts->setActive($active);

        // campers
        $active =
            $route === 'user_profile_camper_list'   || $route === 'user_profile_camper_create' || $route === 'user_profile_camper_read' ||
            $route === 'user_profile_camper_update' || $route === 'user_profile_camper_delete'
        ;

        $text = $this->translator->trans('route.user_profile_camper_list');
        $url = $this->urlGenerator->generate('user_profile_camper_list');
        $itemCampers = new MenuType('user_profile_camper_list', 'navbar_user_profile_item', $text, $url);
        $menu->addChild($itemCampers);
        $itemCampers->setActive($active);

        // applications
        $active = $route === 'user_profile_application_list' || $route === 'user_profile_application_read';

        $text = $this->translator->trans('route.user_profile_application_list');
        $url = $this->urlGenerator->generate('user_profile_application_list');
        $itemApplications = new MenuType('user_profile_application_list', 'navbar_user_profile_item', $text, $url);
        $menu->addChild($itemApplications);
        $itemApplications->setActive($active);

        // password set
        if ($user->getPassword() === null)
        {
            $text = $this->translator->trans('route.user_profile_password_change_create');
            $url = $this->urlGenerator->generate('user_profile_password_change_create');
            $itemPassword = new MenuType('user_profile_password_change_create', 'navbar_user_profile_item', $text, $url);
            $menu->addChild($itemPassword);
            $itemPassword->setActive($route === 'user_profile_password_change_create');
        }
        // password change
        else
        {
            $text = $this->translator->trans('route.user_profile_password_change');
            $url = $this->urlGenerator->generate('user_profile_password_change');
            $itemPassword = new MenuType('user_profile_password_change', 'navbar_user_profile_item', $text, $url);
            $menu->addChild($itemPassword);
            $itemPassword->setActive($route === 'user_profile_password_change');
        }

        return $menu;
    }
}