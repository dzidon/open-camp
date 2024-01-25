import { Controller } from '@hotwired/stimulus';

/**
 * Controller for camper form fill modal.
 */
export default class ModalCamperLoadController extends Controller
{
    static targets = ['select', 'buttonClose', 'buttonLoad'];

    static values = {
        nameFirstId: String,
        nameLastId: String,
        nationalIdentifierId: String,
        isNationalIdentifierAbsentId: String,
        bornAtId: String,
        genderId: String,
        dietaryRestrictionsId: String,
        healthRestrictionsId: String,
        medicationId: String,
    }

    connect()
    {
        this.select = $(this.selectTarget);
        this.buttonClose = $(this.buttonCloseTarget);
        this.buttonLoad = $(this.buttonLoadTarget);
        this.modal = $('#' + this.element.id);

        this.modal.on('hidden.bs.modal', () => {
            this.resetSelect();
            this.updateSubmitButtonAvailability();
        });

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

    fillCamperForm()
    {
        const selectedOption = this.select.find('option:selected');
        const camperInfo = selectedOption.data('camper-json');

        const nameFirstInput = $('#' + this.nameFirstIdValue);
        const nameLastInput = $('#' + this.nameLastIdValue);
        const nationalIdentifierInput = $('#' + this.nationalIdentifierIdValue);
        const isNationalIdentifierAbsentInput = $('#' + this.isNationalIdentifierAbsentIdValue);
        const bornAtInput = $('#' + this.bornAtIdValue);
        const genderInput = $('#' + this.genderIdValue);
        const dietaryRestrictionsInput = $('#' + this.dietaryRestrictionsIdValue);
        const healthRestrictionsInput = $('#' + this.healthRestrictionsIdValue);
        const medicationInput = $('#' + this.medicationIdValue);

        if (!camperInfo)
        {
            return;
        }

        if (nameFirstInput)
        {
            nameFirstInput.val(camperInfo.nameFirst);
        }

        if (nameLastInput)
        {
            nameLastInput.val(camperInfo.nameLast);
        }

        if (nationalIdentifierInput)
        {
            nationalIdentifierInput.val(camperInfo.nationalIdentifier);

            if (camperInfo.nationalIdentifier === null || camperInfo.nationalIdentifier === '')
            {
                isNationalIdentifierAbsentInput.prop('checked', true);
            }
            else
            {
                isNationalIdentifierAbsentInput.prop('checked', false);
            }

            this.dispatchInputEvent(this.isNationalIdentifierAbsentIdValue);
        }

        if (bornAtInput)
        {
            bornAtInput.val(camperInfo.bornAt);
        }

        if (genderInput)
        {
            genderInput.find('input[type="radio"][value="' + camperInfo.gender + '"]').prop('checked', true);
        }

        if (dietaryRestrictionsInput)
        {
            dietaryRestrictionsInput.val(camperInfo.dietaryRestrictions);
        }

        if (healthRestrictionsInput)
        {
            healthRestrictionsInput.val(camperInfo.healthRestrictions);
        }

        if (medicationInput)
        {
            medicationInput.val(camperInfo.medication);
        }

        this.modal.modal('hide');
    }

    resetSelect()
    {
        this.select.val('');
    }

    dispatchInputEvent(id)
    {
        const inputInDoc = document.getElementById(id);
        inputInDoc.dispatchEvent(new Event("input"));
    }
}