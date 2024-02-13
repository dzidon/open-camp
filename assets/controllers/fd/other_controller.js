import { Controller } from '@hotwired/stimulus';

/**
 * Handles hiding and showing the "specify ..." input when the user selects "other" in a choice type.
 */
export default class OtherController extends Controller
{
    static targets = ['input', 'otherRow'];

    connect()
    {
        this.onInputChange();
    }

    onInputChange()
    {
        const inputValue = $(this.inputTarget).val();
        const otherRow = $(this.otherRowTarget);

        if (inputValue === 'other')
        {
            otherRow.show();
        }
        else
        {
            otherRow.hide();
        }
    }
}