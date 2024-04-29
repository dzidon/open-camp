import { Controller } from '@hotwired/stimulus';
import tinymce from 'tinymce/tinymce';

/**
 * Controller for editable text content modal.
 */
export default class ModalController extends Controller
{
    static targets = [
        'textAreaNoHtmlWrapper',
        'textAreaNoHtml',
        'textAreaHtmlWrapper',
        'textAreaHtml',
        'spinner',
        'submitButton',
        'error'
    ];

    static values = {
        html: Boolean,
        identifier: String,
        url: String,
    }

    connect()
    {
        this.ajaxRequest = null;
        this.textAreaNoHtmlWrapper = $(this.textAreaNoHtmlWrapperTarget);
        this.textAreaNoHtml = $(this.textAreaNoHtmlTarget);
        this.textAreaHtmlWrapper = $(this.textAreaHtmlWrapperTarget);
        this.textAreaHtml = $(this.textAreaHtmlTarget);
        this.spinner = $(this.spinnerTarget);
        this.submitButton = $(this.submitButtonTarget);
        this.error = $(this.errorTarget);

        this.spinner.hide();
        this.error.hide();
    }

    getTinymceEditor()
    {
        return tinymce.get(this.textAreaHtml.attr('id'));
    }

    open(identifier, html, content)
    {
        this.identifierValue = identifier;
        this.htmlValue = html;

        const tinymceEditor = this.getTinymceEditor();

        if (this.htmlValue)
        {
            this.textAreaHtmlWrapper.show();
            this.textAreaNoHtmlWrapper.hide();
            this.textAreaHtml.val(content);
            tinymceEditor.setContent(content);
            this.textAreaNoHtml.val('');
        }
        else
        {
            this.textAreaHtmlWrapper.hide();
            this.textAreaNoHtmlWrapper.show();
            this.textAreaHtml.val('');
            tinymceEditor.setContent('');
            this.textAreaNoHtml.val(content);
        }

        const selector = '#' + $(this.element).attr('id');
        $(selector).modal('show');
    }

    submit()
    {
        this.spinner.show();
        this.submitButton.prop('disabled', true);

        let content = this.textAreaNoHtml.val();

        if (this.htmlValue)
        {
            const tinymceEditor = this.getTinymceEditor();
            content = tinymceEditor.getContent();
        }

        this.ajaxRequest = $.ajax({
            url: this.urlValue,
            type: 'POST',
            data: {
                'identifier': this.identifierValue,
                'content': content,
            },
            beforeSend: () => {
                this.error.hide();

                if(this.ajaxRequest != null) {
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