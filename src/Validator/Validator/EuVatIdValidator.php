<?php

namespace App\Validator\Validator;

use App\Validator\Constraint\EuVatId;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates EU VAT IDs.
 */
class EuVatIdValidator extends ConstraintValidator
{
    private ?string $euVatIdRegex;

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, ?string $euVatIdRegex)
    {
        $this->translator = $translator;
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
            throw new UnexpectedValueException($constraint, EuVatId::class);
        }

        $vatId = $value;

        if ($vatId !== null && !is_string($vatId))
        {
            throw new UnexpectedValueException($vatId, 'string');
        }

        if ($vatId === null || $vatId === '')
        {
            return;
        }

        if (!preg_match($this->euVatIdRegex, $vatId))
        {
            $message = $this->translator->trans($constraint->message, [], 'validators');

            $this->context
                ->buildViolation($message)
                ->addViolation()
            ;
        }
    }
}