<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Library\FormField\FormFieldOptionsResolver;
use App\Model\Repository\FormFieldRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Admin form field configuration.
 */
#[ORM\Entity(repositoryClass: FormFieldRepository::class)]
class FormField
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255, unique: true)]
    private string $name;

    #[ORM\Column(length: 32)]
    private string $type;

    #[ORM\Column(length: 255)]
    private string $label;

    #[ORM\Column(type: Types::JSON)]
    private array $options;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isRequired = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $help = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isGlobal = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, FormFieldTypeEnum $type, string $label, array $options = [])
    {
        $this->id = Uuid::v4();
        $this->name = $name;
        $this->type = $type->value;
        $this->label = $label;
        $this->options = $this->resolveOptions($options);
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): FormFieldTypeEnum
    {
        return FormFieldTypeEnum::tryFrom($this->type);
    }

    public function setType(FormFieldTypeEnum $type, array $options = []): self
    {
        $this->type = $type->value;
        $this->options = $this->resolveOptions($options);

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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
                sprintf('Option "%s" was not found in form field %s.', $key, $this->name)
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

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(bool $isRequired): self
    {
        $this->isRequired = $isRequired;

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

    public function isGlobal(): bool
    {
        return $this->isGlobal;
    }

    public function setIsGlobal(bool $isGlobal): self
    {
        $this->isGlobal = $isGlobal;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function resolveOptions(array $options): array
    {
        $type = FormFieldTypeEnum::tryFrom($this->type);
        $resolver = new FormFieldOptionsResolver();

        return $resolver->resolve($type, $options);
    }
}