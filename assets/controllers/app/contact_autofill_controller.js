import { Controller } from '@hotwired/stimulus';

/**
 * Controller for application contact auto fill.
 */
export default class ApplicationContactAutofillController extends Controller
{
    static targets = ['email', 'nameFirst', 'nameLast', 'contacts'];

    connect()
    {
        this.contacts = $(this.contactsTarget);
        this.autofillLocked = false;
        this.firstContactInputsInit();

        if (this.contactEmailInput.val()     === '' &&
            this.contactNameFirstInput.val() === '' &&
            this.contactNameLastInput.val()  === '')
        {
            this.fillFirstContact();
        }
        else
        {
            this.autofillLocked = true;
        }
    }

    firstContactInputsInit()
    {
        this.contactEmailInput = this.contacts.find('input[type="email"][name*="email"]:first');
        this.contactNameFirstInput = this.contacts.find('input[type="text"][name*="nameFirst"]:first');
        this.contactNameLastInput = this.contacts.find('input[type="text"][name*="nameLast"]:first');

        this.contactEmailInput.change((e) =>
        {
            if (e.originalEvent)
            {
                this.autofillLocked = true;
            }
        });

        this.contactNameFirstInput.change((e) =>
        {
            if (e.originalEvent)
            {
                this.autofillLocked = true;
            }
        });

        this.contactNameLastInput.change((e) =>
        {
            if (e.originalEvent)
            {
                this.autofillLocked = true;
            }
        });
    }

    fillFirstContact()
    {
        const emailVal = $(this.emailTarget).val();
        const nameFirstVal = $(this.nameFirstTarget).val();
        const nameLastVal = $(this.nameLastTarget).val();

        if (!this.autofillLocked)
        {
            this.contactEmailInput.val(emailVal);
            this.contactNameFirstInput.val(nameFirstVal);
            this.contactNameLastInput.val(nameLastVal);
        }
    }
}