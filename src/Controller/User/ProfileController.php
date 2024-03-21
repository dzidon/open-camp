<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\Common\BillingData;
use App\Library\Data\User\PasswordChangeData;
use App\Library\Data\User\ProfilePasswordChangeData;
use App\Model\Entity\User;
use App\Model\Event\User\User\UserBillingUpdateEvent;
use App\Model\Event\User\User\UserPasswordChangeEvent;
use App\Model\Event\User\UserPasswordChange\UserPasswordChangeCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Common\BillingType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Form\Type\User\ProfilePasswordChangeType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/billing', name: 'user_profile_billing')]
    public function billing(EventDispatcherInterface      $eventDispatcher,
                            DataTransferRegistryInterface $dataTransfer,
                            Request                       $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $billingData = new BillingData($this->getParameter('app.eu_business_data'));
        $dataTransfer->fillData($billingData, $user);

        $form = $this->createForm(BillingType::class, $billingData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.profile_billing.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new UserBillingUpdateEvent($billingData, $user);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.user.update_billing');

            return $this->redirectToRoute('user_profile_billing');
        }

        return $this->render('user/profile/billing.html.twig', [
            'form_billing' => $form->createView(),
            'breadcrumbs'  => $this->createBreadcrumbs(),
        ]);
    }

    #[Route('/password-change', name: 'user_profile_password_change')]
    public function passwordChange(EventDispatcherInterface $eventDispatcher,
                                   Request                  $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getPassword() === null)
        {
            return $this->redirectToRoute('user_profile_password_change_create');
        }

        $passwordChangeData = new ProfilePasswordChangeData();
        $form = $this->createForm(ProfilePasswordChangeType::class, $passwordChangeData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.profile_password_change.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new UserPasswordChangeEvent($passwordChangeData, $user);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'auth.profile_password_changed');

            return $this->redirectToRoute('user_profile_password_change');
        }

        return $this->render('user/profile/password_change.html.twig', [
            'form_password_change' => $form->createView(),
            'breadcrumbs'          => $this->createBreadcrumbs(),
        ]);
    }

    #[Route('/password-change-create', name: 'user_profile_password_change_create')]
    public function passwordChangeCreate(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getPassword() !== null)
        {
            return $this->redirectToRoute('user_profile_password_change');
        }

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.profile_password_change_create.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $passwordChangeData = new PasswordChangeData();
            $passwordChangeData->setEmail($user->getEmail());
            $event = new UserPasswordChangeCreateEvent($passwordChangeData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'auth.profile_password_change_created', ['email' => $user->getEmail()]);

            return $this->redirectToRoute('user_profile_password_change_create');
        }

        return $this->render('user/profile/password_change_create.html.twig', [
            'form_password_change_create' => $form->createView(),
            'breadcrumbs'                 => $this->createBreadcrumbs(),
        ]);
    }
}