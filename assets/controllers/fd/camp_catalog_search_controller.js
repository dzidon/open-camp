import { Controller } from '@hotwired/stimulus';

/**
 * Handles checking and deactivating the "show only available dates" checkbox when date form fields are updated.
 */
export default class CampCatalogSearchController extends Controller
{
    static targets = ['fromInput', 'toInput', 'isOpenOnlyRow'];

    connect()
    {
        this.checkbox = $(this.isOpenOnlyRowTarget);
        this.onCheckboxChange();
    }

    onCheckboxChange()
    {
        const from = $(this.fromInputTarget).val();
        const to = $(this.toInputTarget).val();

        if (from === '' && to === '')
        {
            this.checkbox.show();
        }
        else
        {
            this.checkbox.hide();
        }
    }
}