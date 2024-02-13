import { Controller } from '@hotwired/stimulus';

/**
 * Controller attached to selects that contain the "other" choice and let the user specify their choice.
 */
export default class OtherInputController extends Controller
{
    static outlets = [ 'cv--content' ];

    connect()
    {
        setTimeout(() => {
            this.updateVisibility();
        }, 0);
    }

    updateVisibility()
    {
        if (!this.hasCvContentOutlet)
        {
            return;
        }

        const inputValue = $(this.element).val();
        const isOther = inputValue === 'other';
        this.cvContentOutlets.forEach(content => content.updateVisibility(isOther));
    }
}