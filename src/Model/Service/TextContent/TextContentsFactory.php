<?php

namespace App\Model\Service\TextContent;

use App\Model\Entity\TextContent;
use App\Model\Repository\TextContentRepositoryInterface;

/**
 * @inheritDoc
 */
class TextContentsFactory implements TextContentsFactoryInterface
{
    const PLACEHOLDER_SHORT = 'Lorem ipsum';
    
    private TextContentRepositoryInterface $textContentRepository;
    
    public function __construct(TextContentRepositoryInterface $textContentRepository)
    {
        $this->textContentRepository = $textContentRepository;
    }

    /**
     * @inheritDoc
     */
    public function createTextContents(): array
    {
        // load existing
        $existingTextContents = [];

        foreach ($this->textContentRepository->findAll() as $existingTextContent)
        {
            $existingTextContents[$existingTextContent->getIdentifier()] = $existingTextContent;
        }

        // create new
        $createdTextContents = [];
        $possibleTextContents = $this->instantiateTextContents();

        foreach ($possibleTextContents as $identifier => $textContent)
        {
            if (!array_key_exists($identifier, $existingTextContents))
            {
                $createdTextContents[] = $textContent;
            }
        }

        return $createdTextContents;
    }

    /**
     * @return TextContent[]
     */
    private function instantiateTextContents(): array
    {
        $paymentMethods['home_page_welcome'] = new TextContent('home_page_welcome', false, self::PLACEHOLDER_SHORT);

        return $paymentMethods;
    }
}