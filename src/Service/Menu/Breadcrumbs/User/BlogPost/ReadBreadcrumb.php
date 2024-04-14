<?php

namespace App\Service\Menu\Breadcrumbs\User\BlogPost;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\BlogPost;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_blog_post_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_blog_post_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var BlogPost $blogPost */
        $blogPost = $options['blog_post'];
        $title = $blogPost->getTitle();
        $id = $blogPost->getId();

        $this->addRoute($breadcrumbs, 'admin_blog_post_read', ['id' => $id])
            ->setText($title)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('blog_post');
        $resolver->setAllowedTypes('blog_post', BlogPost::class);
        $resolver->setRequired('blog_post');
    }
}