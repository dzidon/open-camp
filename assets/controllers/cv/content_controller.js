import { Controller } from '@hotwired/stimulus';

/**
 * Controller attached to an element that can be shown/hidden by updating an input (e.g. a checkbox).
 */
export default class ContentVisibilityController extends Controller
{
    static values = {
        showWhenChosen: Boolean,
    }

    updateVisibility(isChosen)
    {
        const showWhenChosen = this.showWhenChosenValue;
        const content = $(this.element);

        if (isChosen)
        {
            if (showWhenChosen)
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
            if (showWhenChosen)
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