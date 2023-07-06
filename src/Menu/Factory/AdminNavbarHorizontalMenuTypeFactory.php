<?php

namespace App\Menu\Factory;

use App\Entity\User;
use App\Menu\Type\MenuType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the horizontal admin navbar menu.
 */
class AdminNavbarHorizontalMenuTypeFactory extends AbstractMenuTypeFactory
{
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;
    private Security $security;

    public function __construct(TranslatorInterface   $translator,
                                UrlGeneratorInterface $urlGenerator,
                                Security              $security)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'navbar_admin_horizontal';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_admin_horizontal_root');

        /** @var User $user */
        $user = $this->security->getUser();

        // user web
        $text = $this->translator->trans('module.user');
        $url = $this->urlGenerator->generate('user_home');
        $itemHome = new MenuType('user_home', 'navbar_admin_horizontal_item', $text, $url);
        $menu->addChild($itemHome);

        // profile
        $text = $user->getEmail();
        $url = $this->urlGenerator->generate('admin_profile');
        $itemProfile = new MenuType('admin_profile', 'navbar_admin_horizontal_item', $text, $url);
        $menu->addChild($itemProfile);

        // logout
        $text = $this->translator->trans('route.user_logout');
        $url = $this->urlGenerator->generate('user_logout');
        $itemLogout = new MenuType('user_logout', 'navbar_admin_horizontal_item', $text, $url);
        $menu->addChild($itemLogout);

        return $menu;
    }
}