<?php

namespace App\Model\EventSubscriber\Admin\TextContent;

use App\Model\Event\Admin\TextContent\TextContentsCreateEvent;
use App\Model\Repository\TextContentRepositoryInterface;
use App\Model\Service\TextContent\TextContentsFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TextContentsCreateSubscriber
{
    private TextContentsFactoryInterface $textContentsFactoryInterface;

    private TextContentRepositoryInterface $textContentRepository;

    public function __construct(TextContentsFactoryInterface   $textContentsFactoryInterface,
                                TextContentRepositoryInterface $textContentRepository)
    {
        $this->textContentsFactoryInterface = $textContentsFactoryInterface;
        $this->textContentRepository = $textContentRepository;
    }

    #[AsEventListener(event: TextContentsCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiate(TextContentsCreateEvent $event): void
    {
        $textContents = $this->textContentsFactoryInterface->createTextContents();
        $event->setTextContents($textContents);
    }

    #[AsEventListener(event: TextContentsCreateEvent::NAME, priority: 100)]
    public function onCreateSavePermissionsAndGroups(TextContentsCreateEvent $event): void
    {
        $textContents = $event->getTextContents();
        $isFlush = $event->isFlush();

        foreach ($textContents as $key => $textContent)
        {
            $isLast = $key === array_key_last($textContents);
            $this->textContentRepository->saveTextContent($textContent, $isFlush && $isLast);
        }
    }
}