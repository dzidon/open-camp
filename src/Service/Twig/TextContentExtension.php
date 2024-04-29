<?php

namespace App\Service\Twig;

use App\Model\Repository\TextContentRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds editable text content related functions to Twig.
 */
class TextContentExtension extends AbstractExtension
{
    private TextContentRepositoryInterface $textContentRepository;

    public function __construct(TextContentRepositoryInterface $textContentRepository)
    {
        $this->textContentRepository = $textContentRepository;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_text_content', [$this->textContentRepository, 'findOneByIdentifier']),
        ];
    }
}