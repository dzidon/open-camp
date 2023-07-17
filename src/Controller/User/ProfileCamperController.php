<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Form\DataTransfer\Data\User\CamperData;
use App\Form\DataTransfer\Data\User\CamperSearchData;
use App\Form\DataTransfer\Registry\DataTransferRegistryInterface;
use App\Form\Type\Common\HiddenTrueType;
use App\Form\Type\User\CamperSearchType;
use App\Form\Type\User\CamperType;
use App\Menu\Breadcrumbs\User\ProfileCamperBreadcrumbsInterface;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Model\Repository\CamperRepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
            '_breadcrumbs'      => $this->breadcrumbs->buildList(),
        ]);
    }

    #[Route('/camper/create', name: 'user_profile_camper_create')]
    public function create(DataTransferRegistryInterface $dataTransfer, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $formOptions = [];
        if ($this->getParameter('app.sale_camper_siblings'))
        {
            $formOptions = [
                'choices_siblings' => $this->camperRepository->findByUser($user),
            ];
        }

        $camperData = new CamperData();
        $form = $this->createForm(CamperType::class, $camperData, $formOptions);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.camper.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $camper = $this->camperRepository->createCamper($camperData->getName(), $camperData->getGender(), $camperData->getBornAt(), $user);
            $dataTransfer->fillEntity($camperData, $camper);
            $this->camperRepository->saveCamper($camper, true);
            $this->addTransFlash('success', 'crud.action_performed.camper.create');

            return $this->redirectToRoute('user_profile_camper_list');
        }

        return $this->render('user/profile/camper/update.html.twig', [
            'form_camper' => $form->createView(),
            '_breadcrumbs' => $this->breadcrumbs->buildCreate(),
        ]);
    }

    #[Route('/camper/{id}/read', name: 'user_profile_camper_read', requirements: ['id' => '\d+'])]
    public function read(int $id): Response
    {
        $camper = $this->findCamperOrThrow404($id);
        $this->denyAccessUnlessGranted('camper_read', $camper);

        return $this->render('user/profile/camper/read.html.twig', [
            'camper'       => $camper,
            '_breadcrumbs' => $this->breadcrumbs->buildRead($camper->getId()),
        ]);
    }

    #[Route('/camper/{id}/update', name: 'user_profile_camper_update', requirements: ['id' => '\d+'])]
    public function update(DataTransferRegistryInterface $dataTransfer, Request $request, int $id): Response
    {
        $camper = $this->findCamperOrThrow404($id);
        $this->denyAccessUnlessGranted('camper_update', $camper);

        $formOptions = [];
        if ($this->getParameter('app.sale_camper_siblings') === true)
        {
            $formOptions = [
                'choices_siblings' => $this->camperRepository->findOwnedBySameUser($camper),
            ];
        }

        $camperData = new CamperData();
        $dataTransfer->fillData($camperData, $camper);
        $form = $this->createForm(CamperType::class, $camperData, $formOptions);
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
            '_breadcrumbs' => $this->breadcrumbs->buildUpdate($camper->getId()),
        ]);
    }

    #[Route('/camper/{id}/delete', name: 'user_profile_camper_delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request, int $id): Response
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
            'form_delete'  => $form->createView(),
            '_breadcrumbs' => $this->breadcrumbs->buildDelete($camper->getId()),
        ]);
    }

    private function findCamperOrThrow404(int $id): Camper
    {
        $camper = $this->camperRepository->findOneById($id);
        if ($camper === null)
        {
            throw $this->createNotFoundException();
        }

        return $camper;
    }
}