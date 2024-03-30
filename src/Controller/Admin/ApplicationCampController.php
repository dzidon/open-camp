<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\ApplicationCampSearchData;
use App\Model\Entity\Camp;
use App\Model\Entity\User;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use App\Service\Form\Type\Admin\ApplicationCampSearchType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ApplicationCampController extends AbstractController
{
    private CampRepositoryInterface $campRepository;

    private CampCategoryRepositoryInterface $campCategoryRepository;

    public function __construct(CampRepositoryInterface $campRepository, CampCategoryRepositoryInterface $campCategoryRepository)
    {
        $this->campRepository = $campRepository;
        $this->campCategoryRepository = $campCategoryRepository;
    }

    #[IsGranted(new Expression('is_granted("application", "any_admin_permission")         or 
                                is_granted("application_payment", "any_admin_permission") or
                                is_granted("guide_access_read")'))]
    #[Route('/admin/application-camps', name: 'admin_application_camp_list')]
    public function list(FormFactoryInterface             $formFactory,
                         MenuTypeFactoryRegistryInterface $menuFactory,
                         Request                          $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $page = (int) $request->query->get('page', 1);
        $pageSize = 20;

        $searchData = new ApplicationCampSearchData();
        $form = $formFactory->createNamed('', ApplicationCampSearchType::class, $searchData);
        $form->handleRequest($request);
        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();

        if ($isSearchInvalid)
        {
            $searchData = new ApplicationCampSearchData();
        }

        if ($this->isGranted('application', 'any_admin_permission') ||
            $this->isGranted('application_payment', 'any_admin_permission'))
        {
            $result = $this->campRepository->getAdminApplicationCampsResult($searchData, null, $page, $pageSize);
        }
        else
        {
            $result = $this->campRepository->getAdminApplicationCampsResult($searchData, $user, $page, $pageSize);
        }

        $paginator = $result->getPaginator();

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        // load all camp categories so that the camp category path does not trigger additional queries
        $this->campCategoryRepository->findAll();

        return $this->render('admin/application/camp_list.html.twig', [
            'form_search'       => $form->createView(),
            'search_result'     => $result,
            'pagination_menu'   => $paginationMenu,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('application_summary_read')]
    #[Route('/admin/application-camp/{campId}/summary', name: 'admin_application_camp_summary')]
    public function summary(ApplicationRepositoryInterface $applicationRepository, UuidV4 $campId): Response
    {
        $camp = $this->findCampOrThrow404($campId);
        $totalRevenueResult = $applicationRepository->getTotalRevenueForCampResult($camp);
        $numberOfAcceptedApplications = $applicationRepository->getNumberOfAcceptedApplicationsForCamp($camp);

        // load all camp categories so that the camp category path does not trigger additional queries
        $this->campCategoryRepository->findAll();

        return $this->render('admin/application/camp_summary.html.twig', [
            'camp'                            => $camp,
            'total_revenue'                   => $totalRevenueResult->toString(),
            'number_of_accepted_applications' => $numberOfAcceptedApplications,
            'breadcrumbs'                     => $this->createBreadcrumbs([
                'camp' => $camp,
            ]),
        ]);
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