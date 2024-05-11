<?php

namespace App\Service\Validator;

use App\Library\Constraint\EuCin;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates EU CIN numbers.
 */
class EuCinValidator extends ConstraintValidator
{
    private ?string $euCinRegex;

    public function __construct(
        #[Autowire('%app.eu_cin_regex%')]
        ?string $euCinRegex
    ) {
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
            throw new UnexpectedTypeException($constraint, EuCin::class);
        }

        $cin = $value;

        if ($cin !== null && !is_string($cin))
        {
            throw new UnexpectedTypeException($cin, 'string');
        }

        if ($cin === null || $cin === '')
        {
            return;
        }

        if (!preg_match($this->euCinRegex, $cin))
        {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}