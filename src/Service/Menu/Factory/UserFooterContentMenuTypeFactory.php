<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the user footer content menu.
 */
class UserFooterContentMenuTypeFactory extends AbstractMenuTypeFactory
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
        return 'navbar_user_footer_content';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_user_footer_root');

        $text = $this->translator->trans('route.user_blog_post_list');
        $url = $this->urlGenerator->generate('user_blog_post_list');
        $itemBlog = new MenuType('user_blog_post_list', 'navbar_user_footer_item', $text, $url);
        $menu->addChild($itemBlog);

        $text = $this->translator->trans('route.user_gallery_image_list');
        $url = $this->urlGenerator->generate('user_gallery_image_list');
        $itemGallery = new MenuType('user_gallery_image_list', 'navbar_user_footer_item', $text, $url);
        $menu->addChild($itemGallery);

        $text = $this->translator->trans('route.user_guide_list');
        $url = $this->urlGenerator->generate('user_guide_list');
        $itemGuides = new MenuType('user_guide_list', 'navbar_user_footer_item', $text, $url);
        $menu->addChild($itemGuides);

        $text = $this->translator->trans('route.user_downloadable_file_list');
        $url = $this->urlGenerator->generate('user_downloadable_file_list');
        $itemDownloadableFiles = new MenuType('user_downloadable_file_list', 'navbar_user_footer_item', $text, $url);
        $menu->addChild($itemDownloadableFiles);

        return $menu;
    }
}