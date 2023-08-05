import { Controller } from '@hotwired/stimulus';

/**
 * Controller attached to a form field that can be shown/hidden by updating a checkbox.
 */
export default class FormFieldVisibilityController extends Controller
{
    static values = {
        showWhenChecked: Boolean,
    }

    updateVisibility(isChecked)
    {
        const showWhenChecked = this.showWhenCheckedValue;
        const formField = $(this.element);

        if (isChecked)
        {
            if (showWhenChecked)
            {
                formField.show();
            }
            else
            {
                formField.hide();
            }
        }
        else
        {
            if (showWhenChecked)
            {
                formField.hide();
            }
            else
            {
                formField.show();
            }
        }
    }
}