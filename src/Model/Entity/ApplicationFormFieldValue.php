<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Library\FormField\FormFieldOptionsResolver;
use App\Model\Repository\ApplicationFormFieldValueRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Custom form field value attached to an application.
 */
#[ORM\Entity(repositoryClass: ApplicationFormFieldValueRepository::class)]
class ApplicationFormFieldValue
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 32)]
    private string $type;

    #[ORM\Column(length: 255)]
    private string $label;

    #[ORM\Column(type: Types::JSON)]
    private array $options;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isRequired;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $help;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priority;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $value = null;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'applicationFormFieldValues')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Application $application;

    #[ORM\ManyToOne(targetEntity: ApplicationCamper::class, inversedBy: 'applicationFormFieldValues')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?ApplicationCamper $applicationCamper;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(FormFieldTypeEnum  $type,
                                string             $label,
                                ?string            $help,
                                int                $priority,
                                bool               $isRequired,
                                ?Application       $application,
                                ?ApplicationCamper $applicationCamper,
                                array              $options)
    {
        $this->application = $application;
        $this->applicationCamper = $applicationCamper;
        $this->assertApplicationAndApplicationCamper();

        $this->application?->addApplicationFormFieldValue($this);
        $this->applicationCamper?->addApplicationFormFieldValue($this);

        $this->id = Uuid::v4();
        $this->type = $type->value;
        $this->label = $label;
        $this->help = $help;
        $this->priority = $priority;
        $this->isRequired = $isRequired;
        $this->options = $this->resolveOptions($options);
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getType(): FormFieldTypeEnum
    {
        return FormFieldTypeEnum::tryFrom($this->type);
    }

    public function getLabel(): string
    {
        return $this->label;
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
            $idString = $this->id->toRfc4122();

            throw new LogicException(
                sprintf('Option "%s" was not found in application form field value %s.', $key, $idString)
            );
        }

        return $this->options[$key];
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getValueAsString(): string
    {
        $value = $this->getValue();

        if ($value === null)
        {
            return '';
        }

        if (is_array($value))
        {
            return implode(', ', $value);
        }

        return $value;
    }

    public function getValue(): null|array|string
    {
        if ($this->value === null)
        {
            return null;
        }

        $jsonDecoded = json_decode($this->value);

        if (json_last_error() === JSON_ERROR_NONE)
        {
            return $jsonDecoded;
        }

        return $this->value;
    }

    public function setValue(null|array|string $value): self
    {
        if (is_array($value))
        {
            $value = json_encode($value);
        }

        $this->value = $value;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function getApplicationCamper(): ?ApplicationCamper
    {
        return $this->applicationCamper;
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

    private function assertApplicationAndApplicationCamper(): void
    {
        if ($this->application === null && $this->applicationCamper === null)
        {
            throw new LogicException(
                sprintf('%s cannot have $application and $applicationCamper both set to null.', self::class)
            );
        }

        if ($this->application !== null && $this->applicationCamper !== null)
        {
            throw new LogicException(
                sprintf('%s cannot have $application and $applicationCamper both set to not null values.', self::class)
            );
        }
    }
}