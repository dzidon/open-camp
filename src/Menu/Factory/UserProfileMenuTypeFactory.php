<?php

namespace App\Menu\Factory;

use App\Entity\User;
use App\Menu\Type\MenuType;
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

        // applications
        $itemApplications = new MenuType('link1', 'navbar_user_profile_item', 'applications', '#');

        // parents
        $itemParents = new MenuType('link2', 'navbar_user_profile_item', 'parents', '#');

        // campers
        $itemCampers = new MenuType('link3', 'navbar_user_profile_item', 'campers', '#');

        // password set
        if ($user->getPassword() === null)
        {
            $text = $this->translator->trans('route.user_profile_password_change_create');
            $url = $this->urlGenerator->generate('user_profile_password_change_create');
            $itemPassword = new MenuType('user_profile_password_change_create', 'navbar_user_profile_item', $text, $url);
            $itemPassword->setActive($route === 'user_profile_password_change_create');
        }
        // password change
        else
        {
            $text = $this->translator->trans('route.user_profile_password_change');
            $url = $this->urlGenerator->generate('user_profile_password_change');
            $itemPassword = new MenuType('user_profile_password_change', 'navbar_user_profile_item', $text, $url);
            $itemPassword->setActive($route === 'user_profile_password_change');
        }

        $menu
            ->addChild($itemApplications)
            ->addChild($itemParents)
            ->addChild($itemCampers)
            ->addChild($itemPassword)
        ;

        return $menu;
    }
}