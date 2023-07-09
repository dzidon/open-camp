<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Form\DataTransfer\Data\User\ContactData;
use App\Form\DataTransfer\Data\User\ContactSearchData;
use App\Form\DataTransfer\Registry\DataTransferRegistryInterface;
use App\Form\Type\Common\HiddenTrueType;
use App\Form\Type\User\ContactSearchType;
use App\Form\Type\User\ContactType;
use App\Menu\Breadcrumbs\User\ProfileContactBreadcrumbsInterface;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Repository\ContactRepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
            '_breadcrumbs'      => $this->breadcrumbs->buildList(),
        ]);
    }

    #[Route('/contact/create', name: 'user_profile_contact_create')]
    public function create(DataTransferRegistryInterface $dataTransfer, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $contactData = new ContactData();
        $form = $this->createForm(ContactType::class, $contactData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.contact.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $contact = $this->contactRepository->createContact($contactData->getName(), $contactData->getEmail(), $contactData->getPhoneNumber(), $user);
            $dataTransfer->fillEntity($contactData, $contact);
            $this->contactRepository->saveContact($contact, true);
            $this->addTransFlash('success', 'crud.action_performed.Contact.create');

            return $this->redirectToRoute('user_profile_contact_list');
        }

        return $this->render('user/profile/contact/update.html.twig', [
            'form_contact' => $form->createView(),
            '_breadcrumbs' => $this->breadcrumbs->buildCreate(),
        ]);
    }

    #[Route('/contact/{id}/read', name: 'user_profile_contact_read', requirements: ['id' => '\d+'])]
    public function read(int $id): Response
    {
        $contact = $this->findContactOrThrow404($id);
        $this->denyAccessUnlessGranted('contact_read', $contact);

        return $this->render('user/profile/contact/read.html.twig', [
            'contact'      => $contact,
            '_breadcrumbs' => $this->breadcrumbs->buildRead($contact->getId()),
        ]);
    }

    #[Route('/contact/{id}/update', name: 'user_profile_contact_update', requirements: ['id' => '\d+'])]
    public function update(DataTransferRegistryInterface $dataTransfer, Request $request, int $id): Response
    {
        $contact = $this->findContactOrThrow404($id);
        $this->denyAccessUnlessGranted('contact_update', $contact);

        $contactData = new ContactData();
        $dataTransfer->fillData($contactData, $contact);
        $form = $this->createForm(ContactType::class, $contactData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.contact.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($contactData, $contact);
            $this->contactRepository->saveContact($contact, true);
            $this->addTransFlash('success', 'crud.action_performed.Contact.update');

            return $this->redirectToRoute('user_profile_contact_list');
        }

        return $this->render('user/profile/contact/update.html.twig', [
            'form_contact' => $form->createView(),
            '_breadcrumbs' => $this->breadcrumbs->buildUpdate($contact->getId()),
        ]);
    }

    #[Route('/contact/{id}/delete', name: 'user_profile_contact_delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request, int $id): Response
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
            $this->contactRepository->removeContact($contact, true);
            $this->addTransFlash('success', 'crud.action_performed.Contact.delete');

            return $this->redirectToRoute('user_profile_contact_list');
        }

        return $this->render('user/profile/contact/delete.html.twig', [
            'contact'      => $contact,
            'form_delete'  => $form->createView(),
            '_breadcrumbs' => $this->breadcrumbs->buildDelete($contact->getId()),
        ]);
    }

    private function findContactOrThrow404(int $id): Contact
    {
        $contact = $this->contactRepository->findOneById($id);
        if ($contact === null)
        {
            throw $this->createNotFoundException();
        }

        return $contact;
    }
}