<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\ApplicationContactSearchData;
use App\Library\Data\Common\ContactData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationContact;
use App\Model\Event\Admin\ApplicationContact\ApplicationContactCreateEvent;
use App\Model\Event\Admin\ApplicationContact\ApplicationContactDeleteEvent;
use App\Model\Event\Admin\ApplicationContact\ApplicationContactUpdateEvent;
use App\Model\Repository\ApplicationContactRepositoryInterface;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\ApplicationContactSearchType;
use App\Service\Form\Type\Common\ContactType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ApplicationContactController extends AbstractController
{
    private ApplicationContactRepositoryInterface $applicationContactRepository;

    private ApplicationRepositoryInterface $applicationRepository;

    public function __construct(ApplicationContactRepositoryInterface $applicationContactRepository,
                                ApplicationRepositoryInterface        $applicationRepository)
    {
        $this->applicationContactRepository = $applicationContactRepository;
        $this->applicationRepository = $applicationRepository;
    }

    #[Route('/admin/application/{id}/contacts', name: 'admin_application_contact_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request,
                         UuidV4                           $id): Response
    {
        $application = $this->findApplicationOrThrow404($id);
        $this->assertIsGrantedAccess($application);

        $page = (int) $request->query->get('page', 1);
        $searchData = new ApplicationContactSearchData();
        $form = $formFactory->createNamed('', ApplicationContactSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new ApplicationContactSearchData();
        }

        $paginator = $this->applicationContactRepository->getAdminPaginator($searchData, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', [
            'paginator' => $paginator,
        ]);

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/contact/list.html.twig', [
            'application'       => $application,
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs([
                'application' => $application,
                'camp_date'   => $campDate,
                'camp'        => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application/{id}/create-contact', name: 'admin_application_contact_create')]
    public function create(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $application = $this->findApplicationOrThrow404($id);
        $this->assertIsGrantedAccess($application);

        $contactData = new ContactData($application->isEmailMandatory(), $application->isPhoneNumberMandatory());
        $form = $this->createForm(ContactType::class, $contactData);
        $form->add('submit', SubmitType::class, ['label' => 'form.common.contact.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationContactCreateEvent($contactData, $application);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_contact.create');

            return $this->redirectToRoute('admin_application_contact_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/contact/update.html.twig', [
            'application'              => $application,
            'form_application_contact' => $form->createView(),
            'breadcrumbs'              => $this->createBreadcrumbs([
                'application' => $application,
                'camp_date'   => $campDate,
                'camp'        => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-contact/{id}/read', name: 'admin_application_contact_read')]
    public function read(UuidV4 $id): Response
    {
        $applicationContact = $this->findApplicationContactOrThrow404($id);
        $application = $applicationContact->getApplication();
        $this->assertIsGrantedAccess($application);

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/contact/read.html.twig', [
            'application'         => $application,
            'application_contact' => $applicationContact,
            'breadcrumbs'         => $this->createBreadcrumbs([
                'application_contact' => $applicationContact,
                'application'         => $application,
                'camp_date'           => $campDate,
                'camp'                => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-contact/{id}/update', name: 'admin_application_contact_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $applicationContact = $this->findApplicationContactOrThrow404($id);
        $application = $applicationContact->getApplication();
        $this->assertIsGrantedAccess($application);

        $contactData = new ContactData($application->isEmailMandatory(), $application->isPhoneNumberMandatory());
        $dataTransfer->fillData($contactData, $applicationContact);

        $form = $this->createForm(ContactType::class, $contactData);
        $form->add('submit', SubmitType::class, ['label' => 'form.common.contact.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationContactUpdateEvent($contactData, $applicationContact);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_contact.update');

            return $this->redirectToRoute('admin_application_contact_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/contact/update.html.twig', [
            'application'              => $application,
            'application_contact'      => $applicationContact,
            'form_application_contact' => $form->createView(),
            'breadcrumbs'              => $this->createBreadcrumbs([
                'application_contact' => $applicationContact,
                'application'         => $application,
                'camp_date'           => $campDate,
                'camp'                => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-contact/{id}/delete', name: 'admin_application_contact_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $applicationContact = $this->findApplicationContactOrThrow404($id);
        $application = $applicationContact->getApplication();
        $this->assertIsGrantedAccess($application);

        if (count($application->getApplicationContacts()) <= 1)
        {
            $this->addTransFlash('failure', 'crud.error.application_contact_delete');

            return $this->redirectToRoute('admin_application_contact_list', [
                'id' => $application->getId(),
            ]);
        }

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.application_contact_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationContactDeleteEvent($applicationContact);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_contact.delete');

            return $this->redirectToRoute('admin_application_contact_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/contact/delete.html.twig', [
            'application'         => $application,
            'application_contact' => $applicationContact,
            'form_delete'         => $form->createView(),
            'breadcrumbs'         => $this->createBreadcrumbs([
                'application_contact' => $applicationContact,
                'application'         => $application,
                'camp_date'           => $campDate,
                'camp'                => $camp,
            ]),
        ]);
    }

    private function assertIsGrantedAccess(Application $application): void
    {
        if (!$this->isGranted('application_update') && !$this->isGranted('application_guide_update', $application))
        {
            throw $this->createAccessDeniedException();
        }
    }

    private function findApplicationContactOrThrow404(UuidV4 $id): ApplicationContact
    {
        $applicationContact = $this->applicationContactRepository->findOneById($id);

        if ($applicationContact === null)
        {
            throw $this->createNotFoundException();
        }

        return $applicationContact;
    }

    private function findApplicationOrThrow404(UuidV4 $id): Application
    {
        $application = $this->applicationRepository->findOneById($id);

        if ($application === null)
        {
            throw $this->createNotFoundException();
        }

        return $application;
    }
}