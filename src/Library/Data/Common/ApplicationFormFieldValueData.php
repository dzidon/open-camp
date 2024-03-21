<?php

namespace App\Library\Data\Common;

use App\Library\Constraint\ApplicationFormFieldValue;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Library\FormField\FormFieldOptionsResolver;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

#[ApplicationFormFieldValue]
class ApplicationFormFieldValueData
{
    private string $type;

    private bool $isRequired;

    private array $options;

    private int $priority;

    private string $label;

    private ?string $help;

    #[Assert\When(
        expression: 'this.isRequired()',
        constraints: [
            new Assert\NotBlank(),
        ]
    )]
    private null|array|string $value = null;

    public function __construct(FormFieldTypeEnum $type,
                                bool              $isRequired,
                                array             $options,
                                int               $priority,
                                string            $label,
                                ?string           $help = null)
    {
        $this->type = $type->value;
        $this->isRequired = $isRequired;
        $this->options = $this->resolveOptions($options);
        $this->priority = $priority;
        $this->label = $label;
        $this->help = $help;
    }

    public function getType(): FormFieldTypeEnum
    {
        return FormFieldTypeEnum::tryFrom($this->type);
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }

    public function getOption(string $key): mixed
    {
        if (!$this->hasOption($key))
        {
            throw new LogicException(
                sprintf('Option "%s" was not found in "%s".', $key, self::class)
            );
        }

        return $this->options[$key];
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getValue(): null|array|string
    {
        return $this->value;
    }

    public function setValue(null|array|string $value): self
    {
        $this->value = $value;

        return $this;
    }

    private function resolveOptions(array $options): array
    {
        $type = FormFieldTypeEnum::tryFrom($this->type);
        $resolver = new FormFieldOptionsResolver();

        return $resolver->resolve($type, $options);
    }
}