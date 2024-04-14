<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\BlogPostSearchData;
use App\Model\Entity\BlogPost;
use App\Model\Event\User\BlogPost\BlogPostReadEvent;
use App\Model\Repository\BlogPostRepositoryInterface;
use App\Service\Form\Type\User\BlogPostSearchType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Service\Routing\RouteNamerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog')]
class BlogPostController extends AbstractController
{
    private BlogPostRepositoryInterface $blogPostRepository;

    public function __construct(BlogPostRepositoryInterface $blogPostRepository)
    {
        $this->blogPostRepository = $blogPostRepository;
    }

    #[Route('', name: 'user_blog_post_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new BlogPostSearchData();
        $form = $formFactory->createNamed('', BlogPostSearchType::class, $searchData);
        $form->handleRequest($request);
        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();

        if ($isSearchInvalid)
        {
            $searchData = new BlogPostSearchData();
        }

        $showHidden = $this->userCanViewHiddenBlogPosts();
        $paginator = $this->blogPostRepository->getUserPaginator($searchData, $showHidden, $page, 10);

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('user/blog_post/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[Route('/{urlName}', name: 'user_blog_post_read')]
    public function read(RouteNamerInterface $routeNamer, EventDispatcherInterface $eventDispatcher, string $urlName): Response
    {
        $blogPost = $this->findBlogPostOrThrow404($urlName);

        if ($blogPost->isHidden())
        {
            if (!$this->userCanViewHiddenBlogPosts())
            {
                throw $this->createNotFoundException();
            }

            $this->addTransFlash('warning', 'blog.hidden_blog_post_shown_for_admin');
        }

        $event = new BlogPostReadEvent($blogPost);
        $eventDispatcher->dispatch($event, $event::NAME);

        $title = $blogPost->getTitle();
        $routeNamer->setCurrentRouteName($title);

        return $this->render('user/blog_post/read.html.twig', [
            'blog_post' => $blogPost,
            'breadcrumbs' => $this->createBreadcrumbs([
                'blog_post' => $blogPost,
            ]),
        ]);
    }

    private function userCanViewHiddenBlogPosts(): bool
    {
        return
            $this->isGranted('blog_post_read')   ||
            $this->isGranted('blog_post_create') ||
            $this->isGranted('blog_post_update')
        ;
    }

    private function findBlogPostOrThrow404(string $urlName): BlogPost
    {
        $blogPost = $this->blogPostRepository->findOneByUrlName($urlName);

        if ($blogPost === null)
        {
            throw $this->createNotFoundException();
        }

        return $blogPost;
    }
}