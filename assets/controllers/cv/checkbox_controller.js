import { Controller } from '@hotwired/stimulus';

/**
 * Controller attached to checkboxes that update visibility of other elements.
 */
export default class CheckBoxVisibilityController extends Controller
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

        const isChecked = $(this.element).prop('checked');
        this.cvContentOutlets.forEach(content => content.updateVisibility(isChecked));
    }
}