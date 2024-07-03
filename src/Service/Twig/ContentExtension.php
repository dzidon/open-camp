<?php

namespace App\Service\Twig;

use App\Model\Repository\ImageContentRepositoryInterface;
use App\Model\Repository\TextContentRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds functions related to editable page content to Twig.
 */
class ContentExtension extends AbstractExtension
{
    private TextContentRepositoryInterface $textContentRepository;

    private ImageContentRepositoryInterface $imageContentRepository;

    public function __construct(TextContentRepositoryInterface  $textContentRepository,
                                ImageContentRepositoryInterface $imageContentRepository)
    {
        $this->textContentRepository = $textContentRepository;
        $this->imageContentRepository = $imageContentRepository;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_text_content', [$this->textContentRepository, 'findOneByIdentifier']),
            new TwigFunction('get_image_content', [$this->imageContentRepository, 'findOneByIdentifier']),
        ];
    }
}