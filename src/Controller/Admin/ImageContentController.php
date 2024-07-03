<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\ImageContentData;
use App\Model\Event\Admin\ImageContent\ImageContentUpdateEvent;
use App\Model\Repository\ImageContentRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED', statusCode: 403)]
class ImageContentController extends AbstractController
{
    private ImageContentRepositoryInterface $imageContentRepository;

    public function __construct(ImageContentRepositoryInterface $imageContentRepository)
    {
        $this->imageContentRepository = $imageContentRepository;
    }

    #[IsGranted('image_content_update')]
    #[Route('/admin/image-content/update', name: 'admin_image_content_update', methods: ['POST'])]
    public function update(ValidatorInterface       $validator,
                           EventDispatcherInterface $eventDispatcher,
                           Request                  $request): JsonResponse
    {
        $identifier = (string) $request->request->get('identifier', '');
        $imageContent = $this->imageContentRepository->findOneByIdentifier($identifier);

        if ($imageContent === null)
        {
            $errorMessage = $this->trans('api.image_content.update.error.not_found');

            return new JsonResponse(['message' => $errorMessage], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $url = $request->request->get('url');
        $alt = (string) $request->request->get('alt', '');

        if (empty($url))
        {
            $url = null;
        }

        $data = new ImageContentData();
        $data->setUrl($url);
        $data->setAlt($alt);
        $violations = $validator->validate($data);

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation)
        {
            $message = $violation->getMessage();

            return new JsonResponse(['message' => $message], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $event = new ImageContentUpdateEvent($data, $imageContent);
        $eventDispatcher->dispatch($event, $event::NAME);

        return new JsonResponse([
            'identifier' => $imageContent->getIdentifier(),
            'content'    => $imageContent->getUrl(),
        ]);
    }
}