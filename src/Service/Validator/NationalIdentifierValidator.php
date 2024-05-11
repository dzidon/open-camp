<?php

namespace App\Service\Validator;

use App\Library\Constraint\NationalIdentifier;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates national identifiers.
 */
class NationalIdentifierValidator extends ConstraintValidator
{
    private ?string $nationalIdentifierRegex;

    public function __construct(
        #[Autowire('%app.national_identifier_regex%')]
        ?string $nationalIdentifierRegex
    ) {
        $this->nationalIdentifierRegex = $nationalIdentifierRegex;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($this->nationalIdentifierRegex === null)
        {
            return;
        }

        if (!$constraint instanceof NationalIdentifier)
        {
            throw new UnexpectedTypeException($constraint, NationalIdentifier::class);
        }

        $nationalIdentifier = $value;

        if ($nationalIdentifier !== null && !is_string($nationalIdentifier))
        {
            throw new UnexpectedTypeException($nationalIdentifier, 'string');
        }

        if ($nationalIdentifier === null || $nationalIdentifier === '')
        {
            return;
        }

        if (!preg_match($this->nationalIdentifierRegex, $nationalIdentifier))
        {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}