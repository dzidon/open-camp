import { Controller } from '@hotwired/stimulus';

/**
 * Controller attached to checkboxes that update visibility of other form fields.
 */
export default class CheckBoxVisibilityController extends Controller
{
    static outlets = [ 'cv--field' ]

    connect()
    {
        setTimeout(() => {
            this.updateVisibility();
        }, 0);
    }

    updateVisibility()
    {
        if (!this.hasCvFieldOutlet)
        {
            return;
        }

        const isChecked = $(this.element).prop('checked');
        this.cvFieldOutlets.forEach(field => field.updateVisibility(isChecked));
    }
}