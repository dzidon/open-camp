<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use App\Model\Entity\User;
use App\Model\Repository\BlogPostRepositoryInterface;
use App\Model\Repository\DownloadableFileRepositoryInterface;
use App\Model\Repository\GalleryImageRepositoryInterface;
use App\Model\Repository\PageRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the main user website menu.
 */
class UserNavbarMenuTypeFactory extends AbstractMenuTypeFactory
{
    private BlogPostRepositoryInterface $blogPostRepository;

    private GalleryImageRepositoryInterface $galleryImageRepository;

    private UserRepositoryInterface $userRepository;

    private DownloadableFileRepositoryInterface $downloadableFileRepository;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private PageRepositoryInterface $pageRepository;

    private RequestStack $requestStack;

    private Security $security;

    public function __construct(BlogPostRepositoryInterface         $blogPostRepository,
                                GalleryImageRepositoryInterface     $galleryImageRepository,
                                UserRepositoryInterface             $userRepository,
                                DownloadableFileRepositoryInterface $downloadableFileRepository,
                                TranslatorInterface                 $translator,
                                UrlGeneratorInterface               $urlGenerator,
                                PageRepositoryInterface             $pageRepository,
                                RequestStack                        $requestStack,
                                Security                            $security)
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->galleryImageRepository = $galleryImageRepository;
        $this->userRepository = $userRepository;
        $this->downloadableFileRepository = $downloadableFileRepository;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->pageRepository = $pageRepository;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'navbar_user';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->get('_route', '');

        // root
        $menu = new MenuType(self::getMenuIdentifier(), 'navbar_user');

        // home
        $text = $this->translator->trans('route.user_home');
        $url = $this->urlGenerator->generate('user_home');
        $itemHome = new MenuType('user_home', 'navbar_user_item', $text, $url);
        $itemHome->setActive($route === 'user_home');
        $itemHome->setPriority(1000);
        $menu->addChild($itemHome);

        // camp catalog
        $active =
            $route === 'user_camp_catalog'                      || $route === 'user_camp_detail'                 ||
            $route === 'user_application_step_one_create'       || $route === 'user_application_step_one_update' ||
            $route === 'user_application_step_two'              || $route === 'user_application_step_three'      ||
            $route === 'user_application_completed'             || $route === 'user_application_import'          ||
            $route === 'user_application_payment_online_status'
        ;

        $text = $this->translator->trans('route.user_camp_catalog');
        $url = $this->urlGenerator->generate('user_camp_catalog');
        $itemCampCatalog = new MenuType('user_camp_catalog', 'navbar_user_item', $text, $url);
        $itemCampCatalog->setActive($active);
        $itemCampCatalog->setPriority(900);
        $menu->addChild($itemCampCatalog);

        // contact us
        $text = $this->translator->trans('route.user_contact_us');
        $url = $this->urlGenerator->generate('user_contact_us');
        $itemContactUs = new MenuType('user_contact_us', 'navbar_user_item', $text, $url);
        $itemContactUs->setActive($route === 'user_contact_us');
        $itemContactUs->setPriority(800);
        $menu->addChild($itemContactUs);

        // blog
        $includeHiddenBlogPosts = $this->userCanViewHiddenBlogPosts();

        if ($this->blogPostRepository->existsAtLeastOneBlogPost($includeHiddenBlogPosts))
        {
            $text = $this->translator->trans('route.user_blog_post_list');
            $url = $this->urlGenerator->generate('user_blog_post_list');
            $itemBlog = new MenuType('user_blog_post_list', 'navbar_user_item', $text, $url);
            $itemBlog->setActive($route === 'user_blog_post_list' || $route === 'user_blog_post_read');
            $itemBlog->setPriority(700);
            $menu->addChild($itemBlog);
        }

        // gallery
        if ($this->galleryImageRepository->existsAtLeastOneImageInGallery())
        {
            $text = $this->translator->trans('route.user_gallery_image_list');
            $url = $this->urlGenerator->generate('user_gallery_image_list');
            $itemGallery = new MenuType('user_gallery_image_list', 'navbar_user_item', $text, $url);
            $itemGallery->setActive($route === 'user_gallery_image_list' || $route === 'user_gallery_image_read');
            $itemGallery->setPriority(600);
            $menu->addChild($itemGallery);
        }

        // guides
        if ($this->userRepository->existsAtLeastOneGuideWithUrlName())
        {
            $text = $this->translator->trans('route.user_guide_list');
            $url = $this->urlGenerator->generate('user_guide_list');
            $itemGuides = new MenuType('user_guide_list', 'navbar_user_item', $text, $url);
            $itemGuides->setActive($route === 'user_guide_list' || $route === 'user_guide_detail');
            $itemGuides->setPriority(500);
            $menu->addChild($itemGuides);
        }

        // downloadable files
        if ($this->downloadableFileRepository->existsAtLeastOne())
        {
            $text = $this->translator->trans('route.user_downloadable_file_list');
            $url = $this->urlGenerator->generate('user_downloadable_file_list');
            $itemDownloadableFiles = new MenuType('user_downloadable_file_list', 'navbar_user_item', $text, $url);
            $itemDownloadableFiles->setActive($route === 'user_downloadable_file_list');
            $itemDownloadableFiles->setPriority(400);
            $menu->addChild($itemDownloadableFiles);
        }

        // admin
        if ($this->security->isGranted('admin_access'))
        {
            $text = $this->translator->trans('module.admin');
            $url = $this->urlGenerator->generate('admin_home');
            $itemAdmin = new MenuType('admin_home', 'navbar_user_item', $text, $url);
            $itemAdmin->setPriority(300);
            $menu->addChild($itemAdmin);
        }

        // profile, logout
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            /** @var User $user */
            $user = $this->security->getUser();

            // profile - parent
            $text = $user->getEmail();
            $itemProfileParent = new MenuType('parent_profile', 'navbar_user_item', $text, '#');
            $itemProfileParent->setPriority(200);
            $menu->addChild($itemProfileParent);

            // profile - billing
            $active = $route === 'user_profile_billing';

            $text = $this->translator->trans('route.user_profile_billing');
            $url = $this->urlGenerator->generate('user_profile_billing');
            $itemProfileBilling = new MenuType('user_profile_billing', 'navbar_user_dropdown_item', $text, $url);
            $itemProfileParent->addChild($itemProfileBilling);
            $itemProfileBilling->setActive($active, $active);

            // profile - contacts
            $active =
                $route === 'user_profile_contact_list'   || $route === 'user_profile_contact_create' || $route === 'user_profile_contact_read' ||
                $route === 'user_profile_contact_update' || $route === 'user_profile_contact_delete'
            ;

            $text = $this->translator->trans('route.user_profile_contact_list');
            $url = $this->urlGenerator->generate('user_profile_contact_list');
            $itemProfileContacts = new MenuType('user_profile_contact_list', 'navbar_user_dropdown_item', $text, $url);
            $itemProfileParent->addChild($itemProfileContacts);
            $itemProfileContacts->setActive($active, $active);

            // profile - campers
            $active =
                $route === 'user_profile_camper_list'   || $route === 'user_profile_camper_create' || $route === 'user_profile_camper_read' ||
                $route === 'user_profile_camper_update' || $route === 'user_profile_camper_delete'
            ;

            $text = $this->translator->trans('route.user_profile_camper_list');
            $url = $this->urlGenerator->generate('user_profile_camper_list');
            $itemProfileCampers = new MenuType('user_profile_camper_list', 'navbar_user_dropdown_item', $text, $url);
            $itemProfileParent->addChild($itemProfileCampers);
            $itemProfileCampers->setActive($active, $active);

            // profile - applications
            $active = $route === 'user_profile_application_list' || $route === 'user_profile_application_read';

            $text = $this->translator->trans('route.user_profile_application_list');
            $url = $this->urlGenerator->generate('user_profile_application_list');
            $itemProfileApplications = new MenuType('user_profile_application_list', 'navbar_user_dropdown_item', $text, $url);
            $itemProfileParent->addChild($itemProfileApplications);
            $itemProfileApplications->setActive($active, $active);

            // profile - password set
            if ($user->getPassword() === null)
            {
                $active = $route === 'user_profile_password_change_create' || $route === 'user_password_change_complete';

                $text = $this->translator->trans('route.user_profile_password_change_create');
                $url = $this->urlGenerator->generate('user_profile_password_change_create');
                $itemProfilePassword = new MenuType('user_profile_password_change_create', 'navbar_user_dropdown_item', $text, $url);
            }
            // profile - password change
            else
            {
                $active = $route === 'user_profile_password_change' || $route === 'user_password_change_complete';

                $text = $this->translator->trans('route.user_profile_password_change');
                $url = $this->urlGenerator->generate('user_profile_password_change');
                $itemProfilePassword = new MenuType('user_profile_password_change', 'navbar_user_dropdown_item', $text, $url);
            }

            $itemProfileParent->addChild($itemProfilePassword);
            $itemProfilePassword->setActive($active, $active);

            // logout
            $text = $this->translator->trans('route.user_logout');
            $url = $this->urlGenerator->generate('user_logout');
            $itemLogout = new MenuType('user_logout', 'navbar_user_item', $text, $url);
            $itemLogout->setPriority(100);
            $menu->addChild($itemLogout);
        }
        // login
        else
        {
            $active =
                $route === 'user_login'           || $route === 'user_registration' || $route === 'user_registration_complete' ||
                $route === 'user_password_change' || $route === 'user_password_change_complete'
            ;

            $text = $this->translator->trans('route.user_login');
            $url = $this->urlGenerator->generate('user_login');
            $itemLogin = new MenuType('user_login', 'navbar_user_item', $text, $url);
            $itemLogin->setActive($active);
            $itemLogin->setPriority(100);
            $menu->addChild($itemLogin);
        }

        // custom pages
        $userCanViewHiddenPages = $this->userCanViewHiddenPages();
        $pages = $this->pageRepository->findAll();

        foreach ($pages as $page)
        {
            $menuPriorities = $page->getMenuPriorities();

            if (!array_key_exists(self::getMenuIdentifier(), $menuPriorities))
            {
                continue;
            }

            if ($page->isHidden() && !$userCanViewHiddenPages)
            {
                continue;
            }

            $menuPriority = $page->getMenuPriority(self::getMenuIdentifier());

            if ($menuPriority === null)
            {
                $menuPriority = 0;
            }

            $urlName = $page->getUrlName();
            $currentUrlName = ltrim($request->getPathInfo(), '/');
            $active = $route === 'user_page_read' && $urlName === $currentUrlName;

            $title = $page->getTitle();
            $itemIdentifier = 'custom_' . $urlName;
            $url = $this->urlGenerator->generate('user_page_read', ['urlName' => $urlName]);
            $itemCustomPage = new MenuType($itemIdentifier, 'navbar_user_item', $title, $url);
            $itemCustomPage->setPriority($menuPriority);
            $itemCustomPage->setActive($active);
            $menu->addChild($itemCustomPage);
        }

        $menu->sortChildren();

        return $menu;
    }

    private function userCanViewHiddenPages(): bool
    {
        return
            $this->security->isGranted('page_read')   ||
            $this->security->isGranted('page_create') ||
            $this->security->isGranted('page_update')
        ;
    }

    private function userCanViewHiddenBlogPosts(): bool
    {
        return
            $this->security->isGranted('blog_post_read')   ||
            $this->security->isGranted('blog_post_create') ||
            $this->security->isGranted('blog_post_update')
        ;
    }
}