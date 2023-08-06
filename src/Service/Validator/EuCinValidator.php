<?php

namespace App\Service\Validator;

use App\Library\Constraint\EuCin;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates EU CIN numbers.
 */
class EuCinValidator extends ConstraintValidator
{
    private ?string $euCinRegex;

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, ?string $euCinRegex)
    {
        $this->translator = $translator;
        $this->euCinRegex = $euCinRegex;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($this->euCinRegex === null)
        {
            return;
        }

        if (!$constraint instanceof EuCin)
        {
            throw new UnexpectedValueException($constraint, EuCin::class);
        }

        $cin = $value;

        if ($cin !== null && !is_string($cin))
        {
            throw new UnexpectedValueException($cin, 'string');
        }

        if ($cin === null || $cin === '')
        {
            return;
        }

        if (!preg_match($this->euCinRegex, $cin))
        {
            $message = $this->translator->trans($constraint->message, [], 'validators');

            $this->context
                ->buildViolation($message)
                ->addViolation()
            ;
        }
    }
}