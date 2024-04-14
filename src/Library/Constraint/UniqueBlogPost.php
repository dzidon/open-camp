<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueBlogPostValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered url name is not yet assigned to any blog post.
 */
#[Attribute]
class UniqueBlogPost extends Constraint
{
    public string $message = 'unique_blog_post';
    public string $urlNameProperty = 'urlName';
    public string $blogPostProperty = 'blogPost';

    public function __construct(string $message = null,
                                string $urlNameProperty = null,
                                string $blogPostProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->urlNameProperty = $urlNameProperty ?? $this->urlNameProperty;
        $this->blogPostProperty = $blogPostProperty ?? $this->blogPostProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueBlogPostValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}