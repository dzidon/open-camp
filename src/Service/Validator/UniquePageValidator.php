<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniquePage;
use App\Model\Entity\Page;
use App\Model\Repository\PageRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered url name is not yet assigned to any page.
 */
class UniquePageValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    
    private PageRepositoryInterface $pageRepository;

    public function __construct(PropertyAccessorInterface $propertyAccessor, PageRepositoryInterface $pageRepository)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniquePage)
        {
            throw new UnexpectedTypeException($constraint, UniquePage::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $pageData = $value;
        $urlName = $this->propertyAccessor->getValue($pageData, $constraint->urlNameProperty);

        if ($urlName !== null && !is_string($urlName))
        {
            throw new UnexpectedTypeException($urlName, 'string');
        }

        $page = $this->propertyAccessor->getValue($pageData, $constraint->pageProperty);

        if ($page !== null && !$page instanceof Page)
        {
            throw new UnexpectedTypeException($page, Page::class);
        }

        if ($urlName === null || $urlName === '')
        {
            return;
        }

        $existingPage = $this->pageRepository->findOneByUrlName($urlName);

        if ($existingPage === null)
        {
            return;
        }

        $id = $page?->getId();
        $existingId = $existingPage->getId();

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