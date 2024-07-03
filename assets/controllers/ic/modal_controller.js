import { Controller } from '@hotwired/stimulus';

/**
 * Controller for editable image content modal.
 */
export default class ModalController extends Controller
{
    static targets = [
        'form',
        'textInput',
        'altInput',
        'spinner',
        'submitButton',
        'error'
    ];

    static values = {
        identifier: String,
        url: String,
    };

    connect()
    {
        this.ajaxRequest = null;
        this.form = $(this.formTarget);
        this.textInput = $(this.textInputTarget);
        this.altInput = $(this.altInputTarget);
        this.spinner = $(this.spinnerTarget);
        this.submitButton = $(this.submitButtonTarget);
        this.error = $(this.errorTarget);

        this.spinner.hide();
        this.error.hide();

        this.form.on('submit', (event) => {
            event.preventDefault();
            this.submit();
        });

        this.getModal().on('show.bs.modal', () => {
            this.error.hide();
        });
    }

    getModal()
    {
        const selector = '#' + $(this.element).attr('id');

        return $(selector);
    }

    open(identifier, url, alt)
    {
        this.identifierValue = identifier;
        this.textInput.val(url);
        this.altInput.val(alt);

        this.getModal().modal('show');
    }

    submit()
    {
        this.spinner.show();
        this.submitButton.prop('disabled', true);

        const url = this.textInput.val();
        const alt = this.altInput.val();

        this.ajaxRequest = $.ajax({
            url: this.urlValue,
            type: 'POST',
            data: {
                'identifier': this.identifierValue,
                'url': url,
                'alt': alt,
            },
            beforeSend: () => {
                this.error.hide();

                if (this.ajaxRequest != null)
                {
                    this.ajaxRequest.abort();
                }
            },
            error: (response) => {
                this.spinner.hide();
                this.submitButton.prop('disabled', false);

                if ('responseJSON' in response && 'message' in response.responseJSON)
                {
                    const message = response.responseJSON.message;
                    this.error.text(message);
                    this.error.show();
                }
            },
            success: () => {
                location.reload();
            },
        });
    }
}