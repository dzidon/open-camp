<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\BlogPostData;
use App\Library\Data\Admin\BlogPostSearchData;
use App\Model\Entity\BlogPost;
use App\Model\Entity\User;
use App\Model\Event\Admin\BlogPost\BlogPostCreateEvent;
use App\Model\Event\Admin\BlogPost\BlogPostDeleteEvent;
use App\Model\Event\Admin\BlogPost\BlogPostUpdateEvent;
use App\Model\Repository\BlogPostViewRepositoryInterface;
use App\Model\Repository\BlogPostRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\BlogPostSearchType;
use App\Service\Form\Type\Admin\BlogPostType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class BlogPostController extends AbstractController
{
    private BlogPostRepositoryInterface $blogPostRepository;

    public function __construct(BlogPostRepositoryInterface $blogPostRepository)
    {
        $this->blogPostRepository = $blogPostRepository;
    }

    #[IsGranted(new Expression('is_granted("blog_post", "any_admin_permission")'))]
    #[Route('/admin/blog-posts', name: 'admin_blog_post_list')]
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

        $result = $this->blogPostRepository->getAdminSearchResult($searchData, $page, 20);
        $paginator = $result->getPaginator();

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/blog_post/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'result'            => $result,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('blog_post_create')]
    #[Route('/admin/blog-post/create', name: 'admin_blog_post_create')]
    public function create(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = new BlogPostData();

        $form = $this->createForm(BlogPostType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.blog_post.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new BlogPostCreateEvent($data, $user);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.blog_post.create');

            return $this->redirectToRoute('admin_blog_post_list');
        }

        return $this->render('admin/blog_post/update.html.twig', [
            'form_blog_post' => $form->createView(),
            'breadcrumbs'    => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('blog_post_read')]
    #[Route('/admin/blog-post/{id}/read', name: 'admin_blog_post_read')]
    public function read(BlogPostViewRepositoryInterface $blogPostViewRepository, UuidV4 $id): Response
    {
        $blogPost = $this->findBlogPostOrThrow404($id);
        $viewCount = $blogPostViewRepository->getViewCountForBlogPost($blogPost);

        return $this->render('admin/blog_post/read.html.twig', [
            'blog_post'   => $blogPost,
            'view_count'  => $viewCount,
            'breadcrumbs' => $this->createBreadcrumbs([
                'blog_post' => $blogPost,
            ]),
        ]);
    }

    #[IsGranted('blog_post_update')]
    #[Route('/admin/blog-post/{id}/update', name: 'admin_blog_post_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $blogPost = $this->findBlogPostOrThrow404($id);
        $data = new BlogPostData($blogPost);
        $dataTransfer->fillData($data, $blogPost);

        $form = $this->createForm(BlogPostType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.blog_post.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new BlogPostUpdateEvent($data, $blogPost);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.blog_post.update');

            return $this->redirectToRoute('admin_blog_post_list');
        }

        return $this->render('admin/blog_post/update.html.twig', [
            'blog_post'      => $blogPost,
            'form_blog_post' => $form->createView(),
            'breadcrumbs'    => $this->createBreadcrumbs([
                'blog_post' => $blogPost,
            ]),
        ]);
    }

    #[IsGranted('blog_post_delete')]
    #[Route('/admin/blog-post/{id}/delete', name: 'admin_blog_post_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $blogPost = $this->findBlogPostOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.blog_post_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new BlogPostDeleteEvent($blogPost);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.blog_post.delete');

            return $this->redirectToRoute('admin_blog_post_list');
        }

        return $this->render('admin/blog_post/delete.html.twig', [
            'blog_post'   => $blogPost,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs([
                'blog_post' => $blogPost,
            ]),
        ]);
    }

    private function findBlogPostOrThrow404(UuidV4 $id): BlogPost
    {
        $blogPost = $this->blogPostRepository->findOneById($id);
        
        if ($blogPost === null)
        {
            throw $this->createNotFoundException();
        }

        return $blogPost;
    }
}