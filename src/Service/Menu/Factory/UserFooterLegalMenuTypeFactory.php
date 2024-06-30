<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the user footer legal menu.
 */
class UserFooterLegalMenuTypeFactory extends AbstractMenuTypeFactory
{
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(TranslatorInterface   $translator,
                                UrlGeneratorInterface $urlGenerator)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'navbar_user_footer_legal';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_user_footer_root');

        $text = $this->translator->trans('route.user_privacy');
        $url = $this->urlGenerator->generate('user_privacy');
        $itemPrivacy = new MenuType('user_privacy', 'navbar_user_footer_item', $text, $url);
        $menu->addChild($itemPrivacy);

        $text = $this->translator->trans('route.user_terms_of_use');
        $url = $this->urlGenerator->generate('user_terms_of_use');
        $itemTermsOfUse = new MenuType('user_terms_of_use', 'navbar_user_footer_item', $text, $url);
        $menu->addChild($itemTermsOfUse);

        $text = $this->translator->trans('menu.cookie_consent_preferences');
        $url = '#cookie-consent-modal';
        $itemCookieConsents = new MenuType('cookie_consents', 'navbar_user_footer_item', $text, $url);
        $menu->addChild($itemCookieConsents);

        return $menu;
    }
}