<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\BlogPostData;
use App\Model\Entity\BlogPost;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link BlogPostData} to {@link BlogPost} and vice versa.
 */
class BlogPostDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof BlogPostData && $entity instanceof BlogPost;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var BlogPostData $blogPostData */
        /** @var BlogPost $blogPost */
        $blogPostData = $data;
        $blogPost = $entity;

        $blogPostData->setTitle($blogPost->getTitle());
        $blogPostData->setUrlName($blogPost->getUrlName());
        $blogPostData->setDescription($blogPost->getDescription());
        $blogPostData->setContent($blogPost->getContent());
        $blogPostData->setIsHidden($blogPost->isHidden());
        $blogPostData->setIsPinned($blogPost->isPinned());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var BlogPostData $blogPostData */
        /** @var BlogPost $blogPost */
        $blogPostData = $data;
        $blogPost = $entity;

        $blogPost->setTitle($blogPostData->getTitle());
        $blogPost->setUrlName($blogPostData->getUrlName());
        $blogPost->setDescription($blogPostData->getDescription());
        $blogPost->setContent($blogPostData->getContent());
        $blogPost->setIsHidden($blogPostData->isHidden());
        $blogPost->setIsPinned($blogPostData->isPinned());
    }
}