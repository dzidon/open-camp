<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\ProfileData;
use App\Model\Entity\User;
use App\Model\Event\Admin\User\ProfileUpdateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\ProfileType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[Route('/admin/profile')]
class ProfileController extends AbstractController
{
    #[IsGranted('_any_permission')]
    #[Route('', name: 'admin_profile')]
    public function profile(EventDispatcherInterface      $eventDispatcher,
                            DataTransferRegistryInterface $dataTransfer,
                            Request                       $request): Response
    {
        /** @var User $admin */
        $admin = $this->getUser();
        $profileData = new ProfileData($admin);
        $dataTransfer->fillData($profileData, $admin);

        $form = $this->createForm(ProfileType::class, $profileData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.profile.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ProfileUpdateEvent($profileData, $admin);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.user.update_admin_profile');

            return $this->redirectToRoute('admin_profile');
        }

        return $this->render('admin/profile/profile.html.twig', [
            'admin'        => $admin,
            'form_profile' => $form,
            'breadcrumbs'  => $this->createBreadcrumbs(),
        ]);
    }
}