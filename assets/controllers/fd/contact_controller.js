import { Controller } from '@hotwired/stimulus';

/**
 * Handles hiding and showing the "specify role" input when the role select gets updated.
 */
export default class ContactController extends Controller
{
    static targets = ['roleInput', 'roleOtherRow'];

    connect()
    {
        this.onRoleInputChange();
    }

    onRoleInputChange()
    {
        const role = $(this.roleInputTarget).val();
        const roleOtherRow = $(this.roleOtherRowTarget);

        if (role === 'other')
        {
            roleOtherRow.show();
        }
        else
        {
            roleOtherRow.hide();
        }
    }
}