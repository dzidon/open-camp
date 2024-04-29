<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\TextContentData;
use App\Model\Event\Admin\TextContent\TextContentUpdateEvent;
use App\Model\Repository\TextContentRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED', statusCode: 403)]
class TextContentController extends AbstractController
{
    private TextContentRepositoryInterface $textContentRepository;

    public function __construct(TextContentRepositoryInterface $textContentRepository)
    {
        $this->textContentRepository = $textContentRepository;
    }

    #[IsGranted('text_content_update')]
    #[Route('/admin/text-content/update', name: 'admin_text_content_update', methods: ['POST'])]
    public function update(ValidatorInterface       $validator,
                           EventDispatcherInterface $eventDispatcher,
                           Request                  $request): JsonResponse
    {
        $identifier = (string) $request->request->get('identifier', '');
        $content = (string) $request->request->get('content', '');
        $textContent = $this->textContentRepository->findOneByIdentifier($identifier);

        if ($textContent === null)
        {
            $errorMessage = $this->trans('api.text_content.update.error.text_content_not_found');

            return new JsonResponse(['message' => $errorMessage], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = new TextContentData();
        $data->setContent($content);
        $violations = $validator->validate($data);

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation)
        {
            $message = $violation->getMessage();

            return new JsonResponse(['message' => $message], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $event = new TextContentUpdateEvent($data, $textContent);
        $eventDispatcher->dispatch($event, $event::NAME);

        return new JsonResponse([
            'identifier' => $textContent->getIdentifier(),
            'content'    => $textContent->getContent(),
        ]);
    }
}