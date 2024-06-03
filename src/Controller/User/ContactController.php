<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\ContactUsData;
use App\Library\Event\Contact\ContactUsEvent;
use App\Service\Form\Type\User\ContactUsType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact-us', name: 'user_contact_us')]
    public function contactUs(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        $data = new ContactUsData();
        $form = $this->createForm(ContactUsType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.contact_us.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ContactUsEvent($data);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.contact_us.sent');

            return $this->redirectToRoute('user_contact_us');
        }

        return $this->render('user/contact_us/contact_us.html.twig', [
            'form_contact_us' => $form->createView(),
            'breadcrumbs'     => $this->createBreadcrumbs(),
        ]);
    }
}