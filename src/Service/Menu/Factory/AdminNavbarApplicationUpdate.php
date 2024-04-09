<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use App\Model\Entity\Application;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the admin application update menu.
 */
class AdminNavbarApplicationUpdate extends AbstractMenuTypeFactory
{
    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private Security $security;

    private RequestStack $requestStack;

    public function __construct(TranslatorInterface   $translator,
                                UrlGeneratorInterface $urlGenerator,
                                Security              $security,
                                RequestStack          $requestStack)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'navbar_admin_application_update';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        /** @var Application $application */
        $application = $options['application'];
        $applicationId = $application->getId();
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->get('_route', '');

        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_admin_horizontal_update_root');

        if ($this->security->isGranted('application_update')                ||
            $this->security->isGranted('application_state_update')          ||
            $this->security->isGranted('guide_access_update', $application) ||
            $this->security->isGranted('guide_access_state', $application))
        {
            // application update

            $text = $this->translator->trans('route.admin_application_update');
            $url = $this->urlGenerator->generate('admin_application_update', ['id' => $applicationId]);
            $itemApplicationUpdate = new MenuType('admin_application_update', 'navbar_admin_horizontal_update_item', $text, $url);
            $menu->addChild($itemApplicationUpdate);
            $itemApplicationUpdate->setActive($route === 'admin_application_update');
        }

        if ($this->security->isGranted('application_update') || $this->security->isGranted('guide_access_update', $application))
        {
            // application campers

            $active =
                $route === 'admin_application_camper_list' || $route === 'admin_application_camper_create' ||
                $route === 'admin_application_camper_read' || $route === 'admin_application_camper_update' ||
                $route === 'admin_application_camper_delete'
            ;

            $text = $this->translator->trans('route.admin_application_camper_list');
            $url = $this->urlGenerator->generate('admin_application_camper_list', ['id' => $applicationId]);
            $itemApplicationCampers = new MenuType('admin_application_camper_list', 'navbar_admin_horizontal_update_item', $text, $url);
            $menu->addChild($itemApplicationCampers);
            $itemApplicationCampers->setActive($active);

            // application contacts

            $active =
                $route === 'admin_application_contact_list' || $route === 'admin_application_contact_create' ||
                $route === 'admin_application_contact_read' || $route === 'admin_application_contact_update' ||
                $route === 'admin_application_contact_delete'
            ;

            $text = $this->translator->trans('route.admin_application_contact_list');
            $url = $this->urlGenerator->generate('admin_application_contact_list', ['id' => $applicationId]);
            $itemApplicationContacts = new MenuType('admin_application_contact_list', 'navbar_admin_horizontal_update_item', $text, $url);
            $menu->addChild($itemApplicationContacts);
            $itemApplicationContacts->setActive($active);

            // application purchasable items

            if (!empty($application->getApplicationPurchasableItems()))
            {
                $text = $this->translator->trans('route.admin_application_purchasable_items_update');
                $url = $this->urlGenerator->generate('admin_application_purchasable_items_update', ['id' => $applicationId]);
                $itemApplicationPurchasableItemsUpdate = new MenuType('admin_application_purchasable_items_update', 'navbar_admin_horizontal_update_item', $text, $url);
                $menu->addChild($itemApplicationPurchasableItemsUpdate);
                $itemApplicationPurchasableItemsUpdate->setActive($route === 'admin_application_purchasable_items_update');
            }

            // application admin attachments

            $active =
                $route === 'admin_application_admin_attachment_list' || $route === 'admin_application_admin_attachment_create' ||
                $route === 'admin_application_admin_attachment_read' || $route === 'admin_application_admin_attachment_update' ||
                $route === 'admin_application_admin_attachment_delete'
            ;

            $text = $this->translator->trans('route.admin_application_admin_attachment_list');
            $url = $this->urlGenerator->generate('admin_application_admin_attachment_list', ['id' => $applicationId]);
            $itemApplicationAdminAttachments = new MenuType('admin_application_admin_attachment_list', 'navbar_admin_horizontal_update_item', $text, $url);
            $menu->addChild($itemApplicationAdminAttachments);
            $itemApplicationAdminAttachments->setActive($active);
        }

        if ($this->security->isGranted('application_payment', 'any_admin_permission') ||
            $this->security->isGranted('guide_access_payments', $application))
        {
            // application payments

            $active =
                $route === 'admin_application_payment_list'   || $route === 'admin_application_payment_create' ||
                $route === 'admin_application_payment_read'   || $route === 'admin_application_payment_update' ||
                $route === 'admin_application_payment_delete' || $route === 'admin_application_payment_refund'
            ;

            $text = $this->translator->trans('route.admin_application_payment_list');
            $url = $this->urlGenerator->generate('admin_application_payment_list', ['id' => $applicationId]);
            $itemApplicationPayments = new MenuType('admin_application_payment_list', 'navbar_admin_horizontal_update_item', $text, $url);
            $menu->addChild($itemApplicationPayments);
            $itemApplicationPayments->setActive($active);
        }

        $hasActiveItem = false;
        $numberOfItems = 0;

        foreach ($menu->getChildren() as $item)
        {
            $numberOfItems++;

            if ($item->isActive())
            {
                $hasActiveItem = true;
            }
        }

        if (!$hasActiveItem || $numberOfItems === 1)
        {
            $menu = new MenuType(self::getMenuIdentifier(), 'navbar_admin_horizontal_update_root');
        }

        return $menu;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('application');
        $resolver->setAllowedTypes('application', Application::class);
    }
}