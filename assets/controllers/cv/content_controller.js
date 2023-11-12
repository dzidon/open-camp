import { Controller } from '@hotwired/stimulus';

/**
 * Controller attached to an element that can be shown/hidden by updating a checkbox.
 */
export default class ContentVisibilityController extends Controller
{
    static values = {
        showWhenChecked: Boolean,
    }

    updateVisibility(isChecked)
    {
        const showWhenChecked = this.showWhenCheckedValue;
        const content = $(this.element);

        if (isChecked)
        {
            if (showWhenChecked)
            {
                content.show();
            }
            else
            {
                content.hide();
            }
        }
        else
        {
            if (showWhenChecked)
            {
                content.hide();
            }
            else
            {
                content.show();
            }
        }
    }
}