<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\ApplicationCampDateSearchData;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use App\Service\Form\Type\Admin\ApplicationCampDateSearchType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ApplicationCampDateController extends AbstractController
{
    private CampDateRepositoryInterface $campDateRepository;

    public function __construct(CampDateRepositoryInterface $campDateRepository)
    {
        $this->campDateRepository = $campDateRepository;
    }

    #[IsGranted(new Expression('is_granted("application", "any_admin_permission")         or 
                                is_granted("application_payment", "any_admin_permission") or
                                is_granted("camp_date_guide")'))]
    #[Route('/admin/application-camp/{campId}/dates', name: 'admin_application_camp_date_list')]
    public function list(FormFactoryInterface             $formFactory,
                         MenuTypeFactoryRegistryInterface $menuFactory,
                         CampRepositoryInterface          $campRepository,
                         Request                          $request,
                         UuidV4                           $campId): Response
    {
        $camp = $campRepository->findOneById($campId);

        if ($camp === null)
        {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $page = (int) $request->query->get('page', 1);
        $pageSize = 20;

        $searchData = new ApplicationCampDateSearchData();
        $form = $formFactory->createNamed('', ApplicationCampDateSearchType::class, $searchData);
        $form->handleRequest($request);
        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();

        if ($isSearchInvalid)
        {
            $searchData = new ApplicationCampDateSearchData();
        }

        if ($this->isGranted('application', 'any_admin_permission') ||
            $this->isGranted('application_payment', 'any_admin_permission'))
        {
            $result = $this->campDateRepository->getAdminApplicationCampDatesResult($searchData, $camp, null, $page, $pageSize);
        }
        else
        {
            $result = $this->campDateRepository->getAdminApplicationCampDatesResult($searchData, $camp, $user, $page, $pageSize);
        }

        $paginator = $result->getPaginator();

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/application/camp_date_list.html.twig', [
            'form_search'       => $form->createView(),
            'search_result'     => $result,
            'pagination_menu'   => $paginationMenu,
            'is_search_invalid' => $isSearchInvalid,
            'camp'              => $camp,
            'breadcrumbs'       => $this->createBreadcrumbs([
                'camp' => $camp,
            ]),
        ]);
    }

    #[IsGranted('application_summary_read')]
    #[Route('/admin/application-camp-date/{campDateId}/summary', name: 'admin_application_camp_date_summary')]
    public function summary(ApplicationRepositoryInterface       $applicationRepository,
                            ApplicationCamperRepositoryInterface $applicationCamperRepository,
                            UuidV4                               $campDateId): Response
    {
        $campDate = $this->findCampDateOrThrow404($campDateId);
        $camp = $campDate->getCamp();
        $totalRevenueResult = $applicationRepository->getTotalRevenueForCampDateResult($campDate);
        $numberOfAcceptedApplications = $applicationRepository->getNumberOfAcceptedApplicationsForCampDate($campDate);
        $numberOfAcceptedApplicationCampers = $applicationCamperRepository->getNumberOfAcceptedApplicationCampersForCampDate($campDate);
        $applicationCampers = $applicationCamperRepository->findAcceptedByCampDate($campDate);

        return $this->render('admin/application/camp_date_summary.html.twig', [
            'camp_date'                              => $campDate,
            'total_revenue'                          => $totalRevenueResult->toString(),
            'application_campers'                    => $applicationCampers,
            'number_of_accepted_applications'        => $numberOfAcceptedApplications,
            'number_of_accepted_application_campers' => $numberOfAcceptedApplicationCampers,
            'breadcrumbs'                            => $this->createBreadcrumbs([
                'camp'      => $camp,
                'camp_date' => $campDate,
            ]),
        ]);
    }

    private function findCampDateOrThrow404(UuidV4 $id): CampDate
    {
        $campDate = $this->campDateRepository->findOneById($id);

        if ($campDate === null)
        {
            throw $this->createNotFoundException();
        }

        return $campDate;
    }
}