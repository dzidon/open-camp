<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\CampImageData;
use App\Library\Data\Admin\CampImagesUploadData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Repository\CampImageRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\CampImagesUploadType;
use App\Service\Form\Type\Admin\CampImageType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\CampImageBreadcrumbsInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[IsGranted('camp_update')]
class CampImageController extends AbstractController
{
    private CampImageRepositoryInterface $campImageRepository;
    private CampRepositoryInterface $campRepository;
    private CampImageBreadcrumbsInterface $breadcrumbs;

    public function __construct(CampImageRepositoryInterface  $campImageRepository,
                                CampRepositoryInterface       $campRepository,
                                CampImageBreadcrumbsInterface $breadcrumbs)
    {
        $this->campImageRepository = $campImageRepository;
        $this->campRepository = $campRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route('/admin/camp/{id}/images', name: 'admin_camp_image_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         Request                          $request,
                         UuidV4                           $id): Response
    {
        $camp = $this->findCampOrThrow404($id);
        $page = (int) $request->query->get('page', 1);

        $paginator = $this->campImageRepository->getAdminPaginator($camp, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', [
            'paginator' => $paginator,
        ]);

        return $this->render('admin/camp/image/list.html.twig', [
            'camp'            => $camp,
            'pagination_menu' => $paginationMenu,
            'paginator'       => $paginator,
            'breadcrumbs'     => $this->breadcrumbs->buildList($camp->getId()),
        ]);
    }

    #[Route('/admin/camp/{id}/upload-images', name: 'admin_camp_image_upload')]
    public function upload(Request $request, UuidV4 $id): Response
    {
        $camp = $this->findCampOrThrow404($id);

        $campImagesUploadData = new CampImagesUploadData();
        $form = $this->createForm(CampImagesUploadType::class, $campImagesUploadData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.camp_images_upload.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $files = $campImagesUploadData->getImages();

            foreach ($files as $key => $uploadedImage)
            {
                $campImage = $this->campImageRepository->createCampImage($uploadedImage, 0, $camp);
                $this->campImageRepository->saveCampImage($campImage, $key === array_key_last($files));
            }

            $this->addTransFlash('success', 'crud.action_performed.camp_image.upload');

            return $this->redirectToRoute('admin_camp_image_list', [
                'id' => $camp->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/camp/image/upload.html.twig', [
            'camp'        => $camp,
            'form_upload' => $form,
            'breadcrumbs' => $this->breadcrumbs->buildUpload($camp->getId()),
        ]);
    }

    #[Route('/admin/camp-image/{id}/update', name: 'admin_camp_image_update')]
    public function update(DataTransferRegistryInterface $dataTransfer, Request $request, UuidV4 $id): Response
    {
        $campImage = $this->findCampImageOrThrow404($id);
        $camp = $campImage->getCamp();

        $campImageData = new CampImageData();
        $dataTransfer->fillData($campImageData, $campImage);

        $form = $this->createForm(CampImageType::class, $campImageData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.camp_image.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($campImageData, $campImage);
            $this->campImageRepository->saveCampImage($campImage, true);
            $this->addTransFlash('success', 'crud.action_performed.camp_image.update');

            return $this->redirectToRoute('admin_camp_image_list', [
                'id' => $camp->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/camp/image/update.html.twig', [
            'camp'            => $camp,
            'camp_image'      => $campImage,
            'form_camp_image' => $form->createView(),
            'breadcrumbs'     => $this->breadcrumbs->buildUpdate($camp->getId(), $campImage->getId()),
        ]);
    }

    #[Route('/admin/camp-image/{id}/delete', name: 'admin_camp_image_delete')]
    public function delete(Request $request, UuidV4 $id): Response
    {
        $campImage = $this->findCampImageOrThrow404($id);
        $camp = $campImage->getCamp();

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.camp_image_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->campImageRepository->removeCampImage($campImage, true);
            $this->addTransFlash('success', 'crud.action_performed.camp_image.delete');

            return $this->redirectToRoute('admin_camp_image_list', [
                'id' => $camp->getId()->toRfc4122(),
            ]);
        }

        return $this->render('admin/camp/image/delete.html.twig', [
            'camp'        => $camp,
            'camp_image'  => $campImage,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbs->buildDelete($camp->getId(), $campImage->getId()),
        ]);
    }

    private function findCampImageOrThrow404(UuidV4 $id): CampImage
    {
        $campImage = $this->campImageRepository->findOneById($id);
        if ($campImage === null)
        {
            throw $this->createNotFoundException();
        }

        return $campImage;
    }

    private function findCampOrThrow404(UuidV4 $id): Camp
    {
        $camp = $this->campRepository->findOneById($id);
        if ($camp === null)
        {
            throw $this->createNotFoundException();
        }

        return $camp;
    }
}