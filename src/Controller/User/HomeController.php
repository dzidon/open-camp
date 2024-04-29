<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\BlogPostSearchData;
use App\Library\Data\User\CampSearchData;
use App\Model\Repository\BlogPostRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use App\Model\Repository\GalleryImageRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Routing\RouteNamerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'user_home')]
    public function index(GalleryImageRepositoryInterface $galleryImageRepository,
                          CampRepositoryInterface         $campRepository,
                          BlogPostRepositoryInterface     $blogPostRepository,
                          UserRepositoryInterface         $userRepository,
                          RouteNamerInterface             $routeNamer): Response
    {
        $routeNamer->setCurrentRouteName(null);

        // carousel images
        $carouselImages = $galleryImageRepository->findForCarousel();

        // camps
        $campSearchData = new CampSearchData(true);
        $showHiddenCamps = $this->userCanViewHiddenCamps();
        $numberOfCamps = $this->getParameter('app.home_max_number_of_featured_camps');
        $campCatalogResult = null;

        if ($numberOfCamps > 0)
        {
            $campCatalogResult = $campRepository
                ->getUserCampCatalogResult($campSearchData, null, $showHiddenCamps, 1, $numberOfCamps)
            ;
        }

        // blog posts
        $blogPostSearchData = new BlogPostSearchData();
        $showHiddenBlogPosts = $this->userCanViewHiddenBlogPosts();
        $numberOfShownBlogPosts = $this->getParameter('app.home_number_of_blog_posts');
        $blogPosts = [];

        if ($numberOfShownBlogPosts > 0)
        {
            $blogPosts = $blogPostRepository
                ->getUserPaginator($blogPostSearchData, $showHiddenBlogPosts, 1, $numberOfShownBlogPosts)
                ->getCurrentPageItems()
            ;
        }

        // guides
        $numberOfShownGuides = $this->getParameter('app.home_max_number_of_featured_guides');
        $guides = [];

        if ($numberOfShownGuides > 0)
        {
            $guides = $userRepository
                ->getUserGuidePaginator(true, 1, $numberOfShownGuides)
                ->getCurrentPageItems()
            ;
        }

        return $this->render('user/home/index.html.twig', [
            'carousel_images'     => $carouselImages,
            'camp_catalog_result' => $campCatalogResult,
            'blog_posts'          => $blogPosts,
            'guides'              => $guides,
        ]);
    }

    private function userCanViewHiddenCamps(): bool
    {
        return
            $this->isGranted('camp_read')   ||
            $this->isGranted('camp_create') ||
            $this->isGranted('camp_update')
        ;
    }

    private function userCanViewHiddenBlogPosts(): bool
    {
        return
            $this->isGranted('blog_post_read')   ||
            $this->isGranted('blog_post_create') ||
            $this->isGranted('blog_post_update')
        ;
    }
}