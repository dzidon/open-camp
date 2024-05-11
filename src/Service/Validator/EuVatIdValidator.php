<?php

namespace App\Service\Validator;

use App\Library\Constraint\EuVatId;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates EU VAT IDs.
 */
class EuVatIdValidator extends ConstraintValidator
{
    private ?string $euVatIdRegex;

    public function __construct(
        #[Autowire('%app.eu_vat_id_regex%')]
        ?string $euVatIdRegex
    ) {
        $this->euVatIdRegex = $euVatIdRegex;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($this->euVatIdRegex === null)
        {
            return;
        }

        if (!$constraint instanceof EuVatId)
        {
            throw new UnexpectedTypeException($constraint, EuVatId::class);
        }

        $vatId = $value;

        if ($vatId !== null && !is_string($vatId))
        {
            throw new UnexpectedTypeException($vatId, 'string');
        }

        if ($vatId === null || $vatId === '')
        {
            return;
        }

        if (!preg_match($this->euVatIdRegex, $vatId))
        {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}