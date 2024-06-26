<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuIconType;
use App\Library\Menu\MenuType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the vertical admin navbar menu.
 */
class AdminNavbarVerticalMenuTypeFactory extends AbstractMenuTypeFactory
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
        return 'navbar_admin_vertical';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->get('_route', '');

        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_admin_vertical_root');

        // back to user website
        $text = $this->translator->trans('module.user');
        $url = $this->urlGenerator->generate('user_home');
        $itemDashboard = new MenuIconType('user_home', 'navbar_admin_vertical_item', $text, $url, 'fas fa-arrow-left');
        $menu->addChild($itemDashboard);

        // dashboard
        if ($this->security->isGranted('admin_access'))
        {
            $text = $this->translator->trans('route.admin_home');
            $url = $this->urlGenerator->generate('admin_home');
            $itemDashboard = new MenuIconType('admin_home', 'navbar_admin_vertical_item', $text, $url, 'fa-solid fa-gauge');
            $menu->addChild($itemDashboard);
            $itemDashboard->setActive($route === 'admin_home');
        }

        // applications
        $isGrantedApplication = $this->security->isGranted('application', 'any_admin_permission');
        $isGrantedApplicationPayment = $this->security->isGranted('application_payment', 'any_admin_permission');
        $isGrantedApplicationAccessAsGuideList = $this->security->isGranted('guide_access_read');

        if ($isGrantedApplication || $isGrantedApplicationPayment || $isGrantedApplicationAccessAsGuideList)
        {
            $active =
                $route === 'admin_application_camp_list'      || $route === 'admin_application_camp_summary'      ||
                $route === 'admin_application_camp_date_list' || $route === 'admin_application_camp_date_summary' ||

                $route === 'admin_application_list'           || $route === 'admin_application_read'   ||
                $route === 'admin_application_update'         || $route === 'admin_application_delete' ||

                $route === 'admin_application_contact_list'   || $route === 'admin_application_contact_create' ||
                $route === 'admin_application_contact_read'   || $route === 'admin_application_contact_update' ||
                $route === 'admin_application_contact_delete' ||

                $route === 'admin_application_camper_list'   || $route === 'admin_camp_date_application_camper_list' ||
                $route === 'admin_application_camper_create' || $route === 'admin_application_camper_read'           ||
                $route === 'admin_application_camper_update' || $route === 'admin_application_camper_delete'         ||

                $route === 'admin_application_payment_list'   || $route === 'admin_application_payment_refund' ||
                $route === 'admin_application_payment_create' || $route === 'admin_application_payment_read'   ||
                $route === 'admin_application_payment_update' || $route === 'admin_application_payment_delete' ||

                $route === 'admin_application_admin_attachment_list'   || $route === 'admin_application_admin_attachment_create' ||
                $route === 'admin_application_admin_attachment_read'   || $route === 'admin_application_admin_attachment_update' ||
                $route === 'admin_application_admin_attachment_delete' ||

                $route === 'admin_application_purchasable_items_update'
            ;

            $text = $this->translator->trans('route.admin_application_camp_list');
            $url = $this->urlGenerator->generate('admin_application_camp_list');
            $itemApplications = new MenuIconType('admin_application_camp_list', 'navbar_admin_vertical_item', $text, $url, 'fas fa-sticky-note');
            $menu->addChild($itemApplications);
            $itemApplications->setActive($active, $active);
        }

        // components
        $isGrantedFormField = $this->security->isGranted('form_field', 'any_admin_permission');
        $isGrantedAttachmentConfig = $this->security->isGranted('attachment_config', 'any_admin_permission');
        $isGrantedDiscountConfig = $this->security->isGranted('discount_config', 'any_admin_permission');
        $isGrantedTripLocationPath = $this->security->isGranted('trip_location_path', 'any_admin_permission');
        $isGrantedPurchasableItem = $this->security->isGranted('purchasable_item', 'any_admin_permission');

        if ($isGrantedFormField || $isGrantedAttachmentConfig || $isGrantedTripLocationPath || $isGrantedPurchasableItem || $isGrantedDiscountConfig)
        {
            $text = $this->translator->trans('menu_item.navbar_admin_vertical.components');
            $itemComponentsParent = new MenuIconType('parent_applications', 'navbar_admin_vertical_item', $text, '#', 'fas fa-puzzle-piece');
            $menu->addChild($itemComponentsParent);

            if ($isGrantedFormField)
            {
                $active =
                    $route === 'admin_form_field_list'   || $route === 'admin_form_field_create' || $route === 'admin_form_field_read' ||
                    $route === 'admin_form_field_update' || $route === 'admin_form_field_delete'
                ;

                $text = $this->translator->trans('route.admin_form_field_list');
                $url = $this->urlGenerator->generate('admin_form_field_list');
                $itemFormFields = new MenuIconType('admin_form_field_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemComponentsParent->addChild($itemFormFields);
                $itemFormFields->setActive($active, $active);
            }

            if ($isGrantedAttachmentConfig)
            {
                $active =
                    $route === 'admin_attachment_config_list'   || $route === 'admin_attachment_config_create' || $route === 'admin_attachment_config_read' ||
                    $route === 'admin_attachment_config_update' || $route === 'admin_attachment_config_delete'
                ;

                $text = $this->translator->trans('route.admin_attachment_config_list');
                $url = $this->urlGenerator->generate('admin_attachment_config_list');
                $itemAttachmentConfigs = new MenuIconType('admin_attachment_config_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemComponentsParent->addChild($itemAttachmentConfigs);
                $itemAttachmentConfigs->setActive($active, $active);
            }

            if ($isGrantedTripLocationPath)
            {
                $active =
                    $route === 'admin_trip_location_path_list'   || $route === 'admin_trip_location_path_create' || $route === 'admin_trip_location_path_read' ||
                    $route === 'admin_trip_location_path_update' || $route === 'admin_trip_location_path_delete' ||

                    $route === 'admin_trip_location_create' || $route === 'admin_trip_location_read'   ||
                    $route === 'admin_trip_location_update' || $route === 'admin_trip_location_delete'
                ;

                $text = $this->translator->trans('route.admin_trip_location_path_list');
                $url = $this->urlGenerator->generate('admin_trip_location_path_list');
                $itemTripLocationPaths = new MenuIconType('admin_trip_location_path_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemComponentsParent->addChild($itemTripLocationPaths);
                $itemTripLocationPaths->setActive($active, $active);
            }

            if ($isGrantedPurchasableItem)
            {
                $active =
                    $route === 'admin_purchasable_item_list'   || $route === 'admin_purchasable_item_create' || $route === 'admin_purchasable_item_read' ||
                    $route === 'admin_purchasable_item_update' || $route === 'admin_purchasable_item_delete' ||

                    $route === 'admin_purchasable_item_variant_create' || $route === 'admin_purchasable_item_variant_read'   ||
                    $route === 'admin_purchasable_item_variant_update' || $route === 'admin_purchasable_item_variant_delete' ||

                    $route === 'admin_purchasable_item_variant_value_create' || $route === 'admin_purchasable_item_variant_value_read' ||
                    $route === 'admin_purchasable_item_variant_value_update' || $route === 'admin_purchasable_item_variant_value_delete'
                ;

                $text = $this->translator->trans('route.admin_purchasable_item_list');
                $url = $this->urlGenerator->generate('admin_purchasable_item_list');
                $itemPurchasableItems = new MenuIconType('admin_purchasable_item_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemComponentsParent->addChild($itemPurchasableItems);
                $itemPurchasableItems->setActive($active, $active);
            }
            
            if ($isGrantedDiscountConfig)
            {
                $active =
                    $route === 'admin_discount_config_list'   || $route === 'admin_discount_config_create' || $route === 'admin_discount_config_read' ||
                    $route === 'admin_discount_config_update' || $route === 'admin_discount_config_delete'
                ;

                $text = $this->translator->trans('route.admin_discount_config_list');
                $url = $this->urlGenerator->generate('admin_discount_config_list');
                $itemDiscountConfigs = new MenuIconType('admin_discount_config_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemComponentsParent->addChild($itemDiscountConfigs);
                $itemDiscountConfigs->setActive($active, $active);
            }
        }

        // camps
        $isGrantedCamp =
            $this->security->isGranted('camp_create') || $this->security->isGranted('camp_read') ||
            $this->security->isGranted('camp_update') || $this->security->isGranted('camp_delete')
        ;

        $isGrantedCampCategory =
            $this->security->isGranted('camp_category_create') || $this->security->isGranted('camp_category_read') ||
            $this->security->isGranted('camp_category_update') || $this->security->isGranted('camp_category_delete')
        ;

        if ($isGrantedCamp || $isGrantedCampCategory)
        {
            $text = $this->translator->trans('route.admin_camp_list');
            $itemCampsParent = new MenuIconType('parent_camps', 'navbar_admin_vertical_item', $text, '#', 'fas fa-campground');
            $menu->addChild($itemCampsParent);

            if ($isGrantedCamp)
            {
                $active =
                    $route === 'admin_camp_list'          || $route === 'admin_camp_create' || $route === 'admin_camp_read' ||
                    $route === 'admin_camp_update'        || $route === 'admin_camp_delete' ||

                    $route === 'admin_camp_image_list'   || $route === 'admin_camp_image_upload' ||
                    $route === 'admin_camp_image_update' || $route === 'admin_camp_image_delete' ||

                    $route === 'admin_camp_date_list' || $route === 'admin_camp_date_create' ||
                    $route === 'admin_camp_date_read' || $route === 'admin_camp_date_update' ||
                    $route === 'admin_camp_date_delete'
                ;

                $text = $this->translator->trans('menu_item.navbar_admin_vertical.browse');
                $url = $this->urlGenerator->generate('admin_camp_list');
                $itemCamps = new MenuIconType('admin_camp_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemCampsParent->addChild($itemCamps);
                $itemCamps->setActive($active, $active);
            }

            if ($isGrantedCampCategory)
            {
                $active =
                    $route === 'admin_camp_category_list'   || $route === 'admin_camp_category_create' || $route === 'admin_camp_category_read' ||
                    $route === 'admin_camp_category_update' || $route === 'admin_camp_category_delete'
                ;

                $text = $this->translator->trans('route.admin_camp_category_list');
                $url = $this->urlGenerator->generate('admin_camp_category_list');
                $itemCampCategories = new MenuIconType('admin_camp_category_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemCampsParent->addChild($itemCampCategories);
                $itemCampCategories->setActive($active, $active);
            }
        }

        // users
        $isGrantedUser =
            $this->security->isGranted('user_create') || $this->security->isGranted('user_read') ||
            $this->security->isGranted('user_update') || $this->security->isGranted('user_delete') ||
            $this->security->isGranted('user_role_update')
        ;

        $isGrantedRole =
            $this->security->isGranted('role_create') || $this->security->isGranted('role_read') ||
            $this->security->isGranted('role_update') || $this->security->isGranted('role_delete')
        ;

        if ($isGrantedUser || $isGrantedRole)
        {
            $text = $this->translator->trans('route.admin_user_list');
            $itemUsersParent = new MenuIconType('parent_users', 'navbar_admin_vertical_item', $text, '#', 'fas fa-user');
            $menu->addChild($itemUsersParent);

            if ($isGrantedUser)
            {
                $active =
                    $route === 'admin_user_list'   || $route === 'admin_user_create'          || $route === 'admin_user_read' ||
                    $route === 'admin_user_update' || $route === 'admin_user_update_password' || $route === 'admin_user_delete'
                ;

                $text = $this->translator->trans('menu_item.navbar_admin_vertical.browse');
                $url = $this->urlGenerator->generate('admin_user_list');
                $itemUsers = new MenuIconType('admin_user_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemUsersParent->addChild($itemUsers);
                $itemUsers->setActive($active, $active);
            }

            if ($isGrantedRole)
            {
                $active =
                    $route === 'admin_role_list'   || $route === 'admin_role_create' || $route === 'admin_role_read' ||
                    $route === 'admin_role_update' || $route === 'admin_role_delete'
                ;

                $text = $this->translator->trans('route.admin_role_list');
                $url = $this->urlGenerator->generate('admin_role_list');
                $itemRoles = new MenuIconType('admin_role_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemUsersParent->addChild($itemRoles);
                $itemRoles->setActive($active, $active);
            }
        }

        // gallery
        $isGrantedGalleryImageCategory =
            $this->security->isGranted('gallery_image_category_create') || 
            $this->security->isGranted('gallery_image_category_read')   ||
            $this->security->isGranted('gallery_image_category_update') || 
            $this->security->isGranted('gallery_image_category_delete')
        ;

        $isGrantedGalleryImage =
            $this->security->isGranted('gallery_image_create') || $this->security->isGranted('gallery_image_read') ||
            $this->security->isGranted('gallery_image_update') || $this->security->isGranted('gallery_image_delete')
        ;

        if ($isGrantedGalleryImageCategory || $isGrantedGalleryImage)
        {
            $text = $this->translator->trans('route.admin_gallery_image_list');
            $itemGalleryParent = new MenuIconType('parent_gallery', 'navbar_admin_vertical_item', $text, '#', 'fas fa-image');
            $menu->addChild($itemGalleryParent);
            
            if ($isGrantedGalleryImage)
            {
                $active =
                    $route === 'admin_gallery_image_list'   || $route === 'admin_gallery_image_create' ||
                    $route === 'admin_gallery_image_read'   || $route === 'admin_gallery_image_update' ||
                    $route === 'admin_gallery_image_delete'
                ;

                $text = $this->translator->trans('menu_item.navbar_admin_vertical.browse');
                $url = $this->urlGenerator->generate('admin_gallery_image_list');
                $itemGalleryImages = new MenuIconType('admin_gallery_image_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemGalleryParent->addChild($itemGalleryImages);
                $itemGalleryImages->setActive($active, $active);
            }
            
            if ($isGrantedGalleryImageCategory)
            {
                $active =
                    $route === 'admin_gallery_image_category_list'   || $route === 'admin_gallery_image_category_create' ||
                    $route === 'admin_gallery_image_category_read'   || $route === 'admin_gallery_image_category_update' ||
                    $route === 'admin_gallery_image_category_delete' || $route === 'admin_gallery_image_category_truncate'
                ;

                $text = $this->translator->trans('route.admin_gallery_image_category_list');
                $url = $this->urlGenerator->generate('admin_gallery_image_category_list');
                $itemGalleryImageCategories = new MenuIconType('admin_gallery_image_category_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemGalleryParent->addChild($itemGalleryImageCategories);
                $itemGalleryImageCategories->setActive($active, $active);
            }
        }
        
        // content
        $isGrantedBlog =
            $this->security->isGranted('blog_post_create') || $this->security->isGranted('blog_post_read') ||
            $this->security->isGranted('blog_post_update') || $this->security->isGranted('blog_post_delete')
        ;

        $isGrantedPages =
            $this->security->isGranted('page_create') || $this->security->isGranted('page_read') ||
            $this->security->isGranted('page_update') || $this->security->isGranted('page_delete')
        ;

        $isGrantedDownloadableFiles =
            $this->security->isGranted('downloadable_file_create') || $this->security->isGranted('downloadable_file_read') ||
            $this->security->isGranted('downloadable_file_update') || $this->security->isGranted('downloadable_file_delete')
        ;

        if ($isGrantedBlog || $isGrantedPages || $isGrantedDownloadableFiles)
        {
            $text = $this->translator->trans('menu_item.navbar_admin_vertical.content');
            $itemContentParent = new MenuIconType('parent_content', 'navbar_admin_vertical_item', $text, '#', 'fas fa-newspaper');
            $menu->addChild($itemContentParent);

            if ($isGrantedBlog)
            {
                $active =
                    $route === 'admin_blog_post_list' || $route === 'admin_blog_post_create' ||
                    $route === 'admin_blog_post_read' || $route === 'admin_blog_post_update' ||
                    $route === 'admin_blog_post_delete'
                ;

                $text = $this->translator->trans('route.admin_blog_post_list');
                $url = $this->urlGenerator->generate('admin_blog_post_list');
                $itemBlog = new MenuIconType('admin_blog_post_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemContentParent->addChild($itemBlog);
                $itemBlog->setActive($active, $active);
            }

            if ($isGrantedPages)
            {
                $active =
                    $route === 'admin_page_list' || $route === 'admin_page_create' ||
                    $route === 'admin_page_read' || $route === 'admin_page_update' ||
                    $route === 'admin_page_delete'
                ;

                $text = $this->translator->trans('route.admin_page_list');
                $url = $this->urlGenerator->generate('admin_page_list');
                $itemPages = new MenuIconType('admin_page_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemContentParent->addChild($itemPages);
                $itemPages->setActive($active, $active);
            }

            if ($isGrantedDownloadableFiles)
            {
                $active =
                    $route === 'admin_downloadable_file_list' || $route === 'admin_downloadable_file_create' ||
                    $route === 'admin_downloadable_file_read' || $route === 'admin_downloadable_file_update' ||
                    $route === 'admin_downloadable_file_delete'
                ;

                $text = $this->translator->trans('route.admin_downloadable_file_list');
                $url = $this->urlGenerator->generate('admin_downloadable_file_list');
                $itemDownloadableFiles = new MenuIconType('admin_downloadable_file_list', 'navbar_admin_vertical_item', $text, $url, 'far fa-circle');
                $itemContentParent->addChild($itemDownloadableFiles);
                $itemDownloadableFiles->setActive($active, $active);
            }
        }

        return $menu;
    }
}