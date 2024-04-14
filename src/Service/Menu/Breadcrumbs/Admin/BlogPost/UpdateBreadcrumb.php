<?php

namespace App\Service\Menu\Breadcrumbs\Admin\BlogPost;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\BlogPost;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_blog_post_update';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_blog_post_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var BlogPost $blogPost */
        $blogPost = $options['blog_post'];

        $this->addRoute($breadcrumbs, 'admin_blog_post_update', [
            'id' => $blogPost->getId(),
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('blog_post');
        $resolver->setAllowedTypes('blog_post', BlogPost::class);
        $resolver->setRequired('blog_post');
    }
}