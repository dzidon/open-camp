import { Controller } from '@hotwired/stimulus';

/**
 * Controller for contact form fill modal.
 */
export default class ModalContactLoadController extends Controller
{
    static targets = ['select', 'buttonClose', 'buttonLoad'];

    static values = {
        nameFirstId: String,
        nameLastId: String,
        emailId: String,
        phoneNumberId: String,
        roleId: String,
        roleOtherId: String,
    }

    connect()
    {
        this.select = $(this.selectTarget);
        this.buttonClose = $(this.buttonCloseTarget);
        this.buttonLoad = $(this.buttonLoadTarget);

        this.updateSubmitButtonAvailability();
    }

    updateSubmitButtonAvailability()
    {
        const selectVal = this.select.val();

        if (selectVal === '' || selectVal === null)
        {
            this.buttonLoad.attr('disabled','disabled');
        }
        else
        {
            this.buttonLoad.removeAttr('disabled');
        }
    }

    fillContactForm()
    {
        const selectedOption = this.select.find('option:selected');
        const contactInfo = selectedOption.data('contact-json');

        const nameFirstInput = $('#' + this.nameFirstIdValue);
        const nameLastInput = $('#' + this.nameLastIdValue);
        const emailInput = $('#' + this.emailIdValue);
        const phoneNumberInput = $('#' + this.phoneNumberIdValue);
        const roleInput = $('#' + this.roleIdValue);
        const roleOtherInput = $('#' + this.roleOtherIdValue);

        if (!contactInfo)
        {
            return;
        }

        if (nameFirstInput)
        {
            nameFirstInput.val(contactInfo.nameFirst);
            this.dispatchChangeEvent(this.nameFirstIdValue);
        }

        if (nameLastInput)
        {
            nameLastInput.val(contactInfo.nameLast);
            this.dispatchChangeEvent(this.nameLastIdValue);
        }

        if (emailInput)
        {
            emailInput.val(contactInfo.email);
            this.dispatchChangeEvent(this.emailIdValue);
        }

        if (phoneNumberInput)
        {
            phoneNumberInput.val(contactInfo.phoneNumber);
        }

        if (roleInput)
        {
            roleInput.val(contactInfo.role);
            this.dispatchChangeEvent(this.roleIdValue);
        }

        if (roleOtherInput)
        {
            roleOtherInput.val(contactInfo.roleOther);
        }

        $('#' + this.element.id).modal('hide');
        this.resetSelect();
    }

    resetSelect()
    {
        this.select.val('');
        this.updateSubmitButtonAvailability();
    }

    dispatchChangeEvent(id)
    {
        const inputInDoc = document.getElementById(id);
        inputInDoc.dispatchEvent(new Event("change"));
    }
}