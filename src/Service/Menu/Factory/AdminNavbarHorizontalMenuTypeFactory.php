<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use App\Model\Entity\User;
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

        // parent - profile
        $text = $user->getEmail();
        $itemParentProfile = new MenuType('parent_profile', 'navbar_admin_horizontal_item', $text);
        $menu->addChild($itemParentProfile);

        // profile
        $text = $this->translator->trans('route.admin_profile');
        $url = $this->urlGenerator->generate('admin_profile');
        $itemProfile = new MenuType('admin_profile', 'navbar_admin_horizontal_item', $text, $url);
        $itemParentProfile->addChild($itemProfile);

        // logout
        $text = $this->translator->trans('route.user_logout');
        $url = $this->urlGenerator->generate('user_logout');
        $itemLogout = new MenuType('user_logout', 'navbar_admin_horizontal_item', $text, $url);
        $itemParentProfile->addChild($itemLogout);

        return $menu;
    }
}