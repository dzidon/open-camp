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
    const PLACEHOLDER_LONG = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque arcu. Curabitur ligula sapien, pulvinar a vestibulum quis, facilisis vel sapien. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Morbi scelerisque luctus velit. Duis risus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Integer lacinia. Fusce wisi. Quisque tincidunt scelerisque libero. Nam sed tellus id magna elementum tincidunt. Duis pulvinar. In sem justo, commodo ut, suscipit at, pharetra vitae, orci.';
    
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
        $paymentMethods['privacy'] = new TextContent('privacy', true, self::PLACEHOLDER_LONG);
        $paymentMethods['terms_of_use'] = new TextContent('terms_of_use', true, self::PLACEHOLDER_LONG);

        return $paymentMethods;
    }
}