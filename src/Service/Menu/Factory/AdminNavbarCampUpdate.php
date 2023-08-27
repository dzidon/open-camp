<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use App\Model\Entity\Camp;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the admin camp update menu.
 */
class AdminNavbarCampUpdate extends AbstractMenuTypeFactory
{
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;
    private RequestStack $requestStack;

    public function __construct(TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'navbar_admin_camp_update';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        /** @var Camp $camp */
        $camp = $options['camp'];
        $campId = $camp->getId();
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->get('_route', '');

        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_admin_camp_update_root');

        // camp update
        $text = $this->translator->trans('route.admin_camp_update');
        $url = $this->urlGenerator->generate('admin_camp_update', ['id' => $campId->toRfc4122()]);
        $itemCampUpdate = new MenuType('admin_camp_update', 'navbar_admin_camp_update_item', $text, $url);
        $menu->addChild($itemCampUpdate);
        $itemCampUpdate->setActive($route === 'admin_camp_update');

        // camp images
        $active =
            $route === 'admin_camp_image_list'   || $route === 'admin_camp_image_upload' ||
            $route === 'admin_camp_image_update' || $route === 'admin_camp_image_delete'
        ;

        $text = $this->translator->trans('route.admin_camp_image_list');
        $url = $this->urlGenerator->generate('admin_camp_image_list', ['id' => $campId->toRfc4122()]);
        $itemCampImages = new MenuType('admin_camp_image_list', 'navbar_admin_camp_update_item', $text, $url);
        $menu->addChild($itemCampImages);
        $itemCampImages->setActive($active);

        // camp dates
        $active =
            $route === 'admin_camp_date_list'   || $route === 'admin_camp_date_create' || $route === 'admin_camp_date_read' ||
            $route === 'admin_camp_date_update' || $route === 'admin_camp_date_delete'
        ;

        $text = $this->translator->trans('route.admin_camp_date_list');
        $url = $this->urlGenerator->generate('admin_camp_date_list', ['id' => $campId->toRfc4122()]);
        $itemCampDates = new MenuType('admin_camp_date_list', 'navbar_admin_camp_update_item', $text, $url);
        $menu->addChild($itemCampDates);
        $itemCampDates->setActive($active);

        return $menu;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('camp');
        $resolver->setAllowedTypes('camp', Camp::class);
    }
}