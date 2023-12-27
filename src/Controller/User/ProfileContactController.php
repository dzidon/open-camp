<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\ContactData;
use App\Library\Data\User\ContactSearchData;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Event\User\Contact\ContactCreateEvent;
use App\Model\Event\User\Contact\ContactDeleteEvent;
use App\Model\Event\User\Contact\ContactUpdateEvent;
use App\Model\Repository\ContactRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Form\Type\User\ContactSearchType;
use App\Service\Form\Type\User\ContactType;
use App\Service\Menu\Breadcrumbs\User\ProfileContactBreadcrumbsInterface;
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
#[Route('/profile')]
class ProfileContactController extends AbstractController
{
    private ContactRepositoryInterface $contactRepository;
    private ProfileContactBreadcrumbsInterface $breadcrumbs;

    public function __construct(ContactRepositoryInterface         $contactRepository,
                                ProfileContactBreadcrumbsInterface $breadcrumbs)
    {
        $this->contactRepository = $contactRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route('/contacts', name: 'user_profile_contact_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $page = (int) $request->query->get('page', 1);
        $searchData = new ContactSearchData();
        $form = $formFactory->createNamed('', ContactSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new ContactSearchData();
        }

        $paginator = $this->contactRepository->getUserPaginator($searchData, $user, $page, 10);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('user/profile/contact/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->breadcrumbs->buildList(),
        ]);
    }

    #[Route('/contact/create', name: 'user_profile_contact_create')]
    public function create(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $contactData = new ContactData(
            $this->getParameter('app.contact_email_mandatory'),
            $this->getParameter('app.contact_phone_number_mandatory')
        );

        $form = $this->createForm(ContactType::class, $contactData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.contact.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ContactCreateEvent($contactData, $user);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.contact.create');

            return $this->redirectToRoute('user_profile_contact_list');
        }

        return $this->render('user/profile/contact/update.html.twig', [
            'form_contact' => $form->createView(),
            'breadcrumbs'  => $this->breadcrumbs->buildCreate(),
        ]);
    }

    #[Route('/contact/{id}/read', name: 'user_profile_contact_read')]
    public function read(UuidV4 $id): Response
    {
        $contact = $this->findContactOrThrow404($id);
        $this->denyAccessUnlessGranted('contact_read', $contact);

        return $this->render('user/profile/contact/read.html.twig', [
            'contact'     => $contact,
            'breadcrumbs' => $this->breadcrumbs->buildRead($contact),
        ]);
    }

    #[Route('/contact/{id}/update', name: 'user_profile_contact_update')]
    public function update(DataTransferRegistryInterface $dataTransfer,
                           EventDispatcherInterface      $eventDispatcher,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $contact = $this->findContactOrThrow404($id);
        $this->denyAccessUnlessGranted('contact_update', $contact);

        $contactData = new ContactData(
            $this->getParameter('app.contact_email_mandatory'),
            $this->getParameter('app.contact_phone_number_mandatory')
        );
        $dataTransfer->fillData($contactData, $contact);
        $form = $this->createForm(ContactType::class, $contactData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.contact.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ContactUpdateEvent($contactData, $contact);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.contact.update');

            return $this->redirectToRoute('user_profile_contact_list');
        }

        return $this->render('user/profile/contact/update.html.twig', [
            'form_contact' => $form->createView(),
            'breadcrumbs'  => $this->breadcrumbs->buildUpdate($contact),
        ]);
    }

    #[Route('/contact/{id}/delete', name: 'user_profile_contact_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $contact = $this->findContactOrThrow404($id);
        $this->denyAccessUnlessGranted('contact_delete', $contact);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.user.contact_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ContactDeleteEvent($contact);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.contact.delete');

            return $this->redirectToRoute('user_profile_contact_list');
        }

        return $this->render('user/profile/contact/delete.html.twig', [
            'contact'     => $contact,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->breadcrumbs->buildDelete($contact),
        ]);
    }

    private function findContactOrThrow404(UuidV4 $id): Contact
    {
        $contact = $this->contactRepository->findOneById($id);
        if ($contact === null)
        {
            throw $this->createNotFoundException();
        }

        return $contact;
    }
}