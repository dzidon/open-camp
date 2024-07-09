import { Controller } from '@hotwired/stimulus';

/**
 * Controller for "copy url to clipboard" functionality.
 */
export default class CopyController extends Controller
{
    static targets = ['urlInput', 'copyButton', 'successInfo'];

    connect()
    {
        this.urlInput = $(this.urlInputTarget);
        this.copyButton = $(this.copyButtonTarget);
        this.successInfo = $(this.successInfoTarget);

        this.hideSuccessInfoTimerDuration = 5000; // ms
        this.hideSuccessInfoTimer = null;

        this.hideSuccessInfo();
    }

    copyUrlToClipboard()
    {
        const url = this.urlInput.val();
        navigator.clipboard.writeText(url);
        this.showSuccessInfo();

        if (this.hideSuccessInfoTimer !== null)
        {
            clearTimeout(this.hideSuccessInfoTimer);
        }

        this.hideSuccessInfoTimer = setInterval(() => {
            this.hideSuccessInfo();
            clearTimeout(this.hideSuccessInfoTimer);
            this.hideSuccessInfoTimer = null;
        }, this.hideSuccessInfoTimerDuration);
    }

    showSuccessInfo()
    {
        this.successInfo.addClass("d-block");
        this.successInfo.removeClass("d-none");
    }

    hideSuccessInfo()
    {
        this.successInfo.removeClass("d-block");
        this.successInfo.addClass("d-none");
    }
}