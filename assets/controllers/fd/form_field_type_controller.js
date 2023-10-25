import { Controller } from '@hotwired/stimulus';

/**
 * Handles displaying the right fields depending on the chosen type in custom form field edit form.
 */
export default class FormFieldTypeController extends Controller
{
    static targets = ['optionRow', 'typeRow', 'typeSelect', 'spinner'];
    static values = {
        spinner: String,
        url: String
    }

    connect()
    {
        this.ajaxRequest = null;
    }

    loadOptionFields(event)
    {
        const selectElement = event.target;
        const type = selectElement.value;

        this.removeOptionFields();
        this.removeSpinner();
        this.addSpinner();
        this.insertOptionFields(type);
    }

    removeOptionFields()
    {
        this.optionRowTargets.forEach(optionRow => $(optionRow).remove());
    }

    removeSpinner()
    {
        this.spinnerTargets.forEach(spinner => $(spinner).remove());
    }

    addSpinner()
    {
        $(`<div data-fd--form-field-type-target="spinner">${this.spinnerValue}</div>`).insertAfter(this.typeRowTarget);
    }

    insertOptionFields(type)
    {
        this.ajaxRequest = $.ajax({
            url: this.urlValue,
            type: 'GET',
            data: {
                'type': type
            },
            beforeSend : () => {
                if(this.ajaxRequest != null) {
                    this.ajaxRequest.abort();
                }
            },
            success: (json) => {
                this.removeSpinner();

                const form = $(json['form_field_type']);
                $(form.find('[data-fd--form-field-type-target="optionRow"]').get().reverse()).each((key, optionRow) => {
                    $(optionRow).insertAfter(this.typeRowTarget);
                });
            },
        });
    }
}