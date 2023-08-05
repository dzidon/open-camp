<?php

namespace App\Validator\Validator;

use App\Validator\Constraint\NationalIdentifier;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates national identifiers.
 */
class NationalIdentifierValidator extends ConstraintValidator
{
    private ?string $nationalIdentifierRegex;

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, ?string $nationalIdentifierRegex)
    {
        $this->translator = $translator;
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
            throw new UnexpectedValueException($constraint, NationalIdentifier::class);
        }

        $nationalIdentifier = $value;

        if ($nationalIdentifier !== null && !is_string($nationalIdentifier))
        {
            throw new UnexpectedValueException($nationalIdentifier, 'string');
        }

        if ($nationalIdentifier === null || $nationalIdentifier === '')
        {
            return;
        }

        if (!preg_match($this->nationalIdentifierRegex, $nationalIdentifier))
        {
            $message = $this->translator->trans($constraint->message, [], 'validators');

            $this->context
                ->buildViolation($message)
                ->addViolation()
            ;
        }
    }
}