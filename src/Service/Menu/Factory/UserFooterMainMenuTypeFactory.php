<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use App\Model\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the user footer menu.
 */
class UserFooterMainMenuTypeFactory extends AbstractMenuTypeFactory
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
        return 'navbar_user_footer_main';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        /** @var null|User $user */
        $user = $this->security->getUser();
        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_user_footer_root');

        $text = $this->translator->trans('route.user_home');
        $url = $this->urlGenerator->generate('user_home');
        $itemHome = new MenuType('user_home', 'navbar_user_footer_item', $text, $url);
        $menu->addChild($itemHome);

        $text = $this->translator->trans('route.user_camp_catalog');
        $url = $this->urlGenerator->generate('user_camp_catalog');
        $itemCampCatalog = new MenuType('user_camp_catalog', 'navbar_user_footer_item', $text, $url);
        $menu->addChild($itemCampCatalog);

        $text = $this->translator->trans('route.user_contact_us');
        $url = $this->urlGenerator->generate('user_contact_us');
        $itemContactUs = new MenuType('user_contact_us', 'navbar_user_footer_item', $text, $url);
        $menu->addChild($itemContactUs);

        if ($user === null)
        {
            $text = $this->translator->trans('route.user_login');
            $url = $this->urlGenerator->generate('user_login');
            $itemLogin = new MenuType('user_login', 'navbar_user_footer_item', $text, $url);
            $menu->addChild($itemLogin);
        }
        else
        {
            $text = $this->translator->trans('module.profile');
            $url = $this->urlGenerator->generate('user_profile_billing');
            $itemProfile = new MenuType('user_profile_billing', 'navbar_user_footer_item', $text, $url);
            $menu->addChild($itemProfile);
        }

        return $menu;
    }
}