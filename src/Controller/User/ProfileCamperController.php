<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\CamperData;
use App\Library\Data\User\CamperSearchData;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Model\Repository\CamperRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Form\Type\User\CamperSearchType;
use App\Service\Form\Type\User\CamperType;
use App\Service\Menu\Breadcrumbs\User\ProfileCamperBreadcrumbsInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[Route('/profile')]
class ProfileCamperController extends AbstractController
{
    private CamperRepositoryInterface $camperRepository;
    private ProfileCamperBreadcrumbsInterface $breadcrumbs;

    public function __construct(CamperRepositoryInterface         $camperRepository,
                                ProfileCamperBreadcrumbsInterface $breadcrumbs)
    {
        $this->camperRepository = $camperRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route('/campers', name: 'user_profile_camper_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $page = (int) $request->query->get('page', 1);
        $searchData = new CamperSearchData();
        $form = $formFactory->createNamed('', CamperSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new CamperSearchData();
        }

        $paginator = $this->camperRepository->getUserPaginator($searchData, $user, $page, 10);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('user/profile/camper/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->breadcrumbs->buildList(),
        ]);
    }

    #[Route('/camper/create', name: 'user_profile_camper_create')]
    public function create(DataTransferRegistryInterface $dataTransfer, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $camperData = new CamperData($this->getParameter('app.national_identifier'));

        $form = $this->createForm(CamperType::class, $camperData, [
            'choices_siblings' => $this->camperRepository->findByUser($user),
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.camper.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $camper = $this->camperRepository->createCamper($camperData->getNameFirst(), $camperData->getNameLast(), $camperData->getGender(), $camperData->getBornAt(), $user);
            $dataTransfer->fillEntity($camperData, $camper);
            $this->camperRepository->saveCamper($camper, true);
            $this->addTransFlash('success', 'crud.action_performed.camper.create');

            return $this->redirectToRoute('user_profile_camper_list');
        }

        return $this->render('user/profile/camper/update.html.twig', [
            'form_camper' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbs->buildCreate(),
        ]);
    }

    #[Route('/camper/{id}/read', name: 'user_profile_camper_read')]
    public function read(UuidV4 $id): Response
    {
        $camper = $this->findCamperOrThrow404($id);
        $this->denyAccessUnlessGranted('camper_read', $camper);

        return $this->render('user/profile/camper/read.html.twig', [
            'camper'      => $camper,
            'breadcrumbs' => $this->breadcrumbs->buildRead($camper),
        ]);
    }

    #[Route('/camper/{id}/update', name: 'user_profile_camper_update')]
    public function update(DataTransferRegistryInterface $dataTransfer, Request $request, UuidV4 $id): Response
    {
        $camper = $this->findCamperOrThrow404($id);
        $this->denyAccessUnlessGranted('camper_update', $camper);

        $camperData = new CamperData($this->getParameter('app.national_identifier'));
        $dataTransfer->fillData($camperData, $camper);

        $form = $this->createForm(CamperType::class, $camperData, [
            'choices_siblings' => $this->camperRepository->findOwnedBySameUser($camper),
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.camper.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($camperData, $camper);
            $this->camperRepository->saveCamper($camper, true);
            $this->addTransFlash('success', 'crud.action_performed.camper.update');

            return $this->redirectToRoute('user_profile_camper_list');
        }

        return $this->render('user/profile/camper/update.html.twig', [
            'form_camper' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbs->buildUpdate($camper),
        ]);
    }

    #[Route('/camper/{id}/delete', name: 'user_profile_camper_delete')]
    public function delete(Request $request, UuidV4 $id): Response
    {
        $camper = $this->findCamperOrThrow404($id);
        $this->denyAccessUnlessGranted('camper_delete', $camper);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.user.camper_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->camperRepository->removeCamper($camper, true);
            $this->addTransFlash('success', 'crud.action_performed.camper.delete');

            return $this->redirectToRoute('user_profile_camper_list');
        }

        return $this->render('user/profile/camper/delete.html.twig', [
            'camper'      => $camper,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbs->buildDelete($camper),
        ]);
    }

    private function findCamperOrThrow404(UuidV4 $id): Camper
    {
        $camper = $this->camperRepository->findOneById($id);
        if ($camper === null)
        {
            throw $this->createNotFoundException();
        }

        return $camper;
    }
}