<?php

namespace App\Service\EventSubscriber;

use App\Library\Event\Contact\ContactUsEvent;
use App\Service\Mailer\ContactUsMailerInterface;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ContactUsSubscriber
{
    private ContactUsMailerInterface $mailer;

    private PhoneNumberUtil $phoneNumberUtil;

    private string $phoneNumberFormat;

    public function __construct(
        ContactUsMailerInterface $mailer,
        PhoneNumberUtil          $phoneNumberUtil,

        #[Autowire('%app.phone_number_format%')]
        string $phoneNumberFormat
    ) {
        $this->mailer = $mailer;
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->phoneNumberFormat = $phoneNumberFormat;
    }

    #[AsEventListener(event: ContactUsEvent::NAME, priority: 100)]
    public function onContactUsSendEmail(ContactUsEvent $event): void
    {
        $data = $event->getContactUsData();
        $name = $data->getName();
        $email = $data->getEmail();
        $phoneNumber = $data->getPhoneNumber();
        $phoneNumberString = $this->phoneNumberUtil->format($phoneNumber, $this->phoneNumberFormat);
        $message = $data->getMessage();

        $this->mailer->sendEmail($name, $email, $phoneNumberString, $message);
    }
}