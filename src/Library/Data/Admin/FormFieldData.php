<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\Compound\FormFieldChoiceItemRequirements;
use App\Library\Constraint\UniqueFormField;
use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Module\Application\FormField\FormFieldOptionsResolver;
use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueFormField]
class FormFieldData
{
    private ?FormField $formField;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\NotBlank]
    private ?FormFieldTypeEnum $type = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[Assert\Length(max: 255)]
    private ?string $help = null;

    #[Assert\Collection(
        fields: [
            // text
            'length_min' => [
                new Assert\Type(type: 'integer'),
                new Assert\GreaterThanOrEqual(0),
            ],
            'length_max' => [
                new Assert\Type(type: 'integer'),
                new Assert\GreaterThanOrEqual(1),
                new Assert\GreaterThanOrEqual(propertyPath: 'options[length_min]'),
            ],
            'regex' => new Assert\Type(type: 'string'),
            // number
            'min' => new Assert\Type(type: 'float'),
            'max' => [
                new Assert\Type(type: 'float'),
                new Assert\GreaterThanOrEqual(propertyPath: 'options[min]'),
            ],
            'decimal' => new Assert\Type(type: 'bool'),
            // choice
            'multiple' => new Assert\Type(type: 'bool'),
            'expanded' => new Assert\Type(type: 'bool'),
            'items'    => [
                new Assert\When(
                    expression: '!this.isDisableChoiceItemsValidation()',
                    constraints: [
                        new Assert\All(
                            new FormFieldChoiceItemRequirements(),
                        ),
                    ],
                ),
                new Assert\Type(type: 'array'),
                new Assert\NotBlank(message: 'dropdown_items_mandatory'),
            ],
        ],
        allowMissingFields: true,
    )]
    private array $options = [];

    private bool $isRequired = false;

    /** @var bool Choice item validation can be disabled as there is currently no way to map the errors to
     *            corresponding nested CollectionType form fields.
     */
    private bool $disableChoiceItemsValidation;

    public function __construct(?FormField $formField = null, bool $disableChoiceItemsValidation = false)
    {
        $this->formField = $formField;
        $this->disableChoiceItemsValidation = $disableChoiceItemsValidation;
    }

    public function getFormField(): ?FormField
    {
        return $this->formField;
    }

    public function isDisableChoiceItemsValidation(): bool
    {
        return $this->disableChoiceItemsValidation;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?FormFieldTypeEnum
    {
        return $this->type;
    }

    public function setType(?FormFieldTypeEnum $type, array $options = []): self
    {
        $this->type = $type;
        $this->options = $this->resolveOptions($options);

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function setHelp(?string $help): self
    {
        $this->help = $help;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(?bool $isRequired): self
    {
        $this->isRequired = $isRequired;

        return $this;
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
                sprintf('Option "%s" was not found in form field data.', $key)
            );
        }

        return $this->options[$key];
    }

    public function setOption(string $option, mixed $value): self
    {
        $options = $this->options;
        $options[$option] = $value;
        $this->options = $this->resolveOptions($options);

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = $this->resolveOptions(array_merge($this->options, $options));

        return $this;
    }

    private function resolveOptions(array $options): array
    {
        if ($this->type === null)
        {
            return (new OptionsResolver())->resolve($options);
        }

        $resolver = new FormFieldOptionsResolver();

        return $resolver->resolve($this->type, $options);
    }
}