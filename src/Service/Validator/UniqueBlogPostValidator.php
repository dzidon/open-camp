<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueBlogPost;
use App\Model\Entity\BlogPost;
use App\Model\Repository\BlogPostRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered url name is not yet assigned to any blog post.
 */
class UniqueBlogPostValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private BlogPostRepositoryInterface $blogPostRepository;

    public function __construct(PropertyAccessorInterface   $propertyAccessor,
                                BlogPostRepositoryInterface $blogPostRepository)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->blogPostRepository = $blogPostRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueBlogPost)
        {
            throw new UnexpectedTypeException($constraint, UniqueBlogPost::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $blogPostData = $value;
        $urlName = $this->propertyAccessor->getValue($blogPostData, $constraint->urlNameProperty);

        if ($urlName !== null && !is_string($urlName))
        {
            throw new UnexpectedTypeException($urlName, 'string');
        }

        $blogPost = $this->propertyAccessor->getValue($blogPostData, $constraint->blogPostProperty);

        if ($blogPost !== null && !$blogPost instanceof BlogPost)
        {
            throw new UnexpectedTypeException($blogPost, BlogPost::class);
        }

        if ($urlName === null || $urlName === '')
        {
            return;
        }

        $existingBlogPost = $this->blogPostRepository->findOneByUrlName($urlName);

        if ($existingBlogPost === null)
        {
            return;
        }

        $id = $blogPost?->getId();
        $existingId = $existingBlogPost->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->urlNameProperty)
                ->addViolation()
            ;
        }
    }
}