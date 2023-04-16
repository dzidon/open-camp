import { Controller } from '@hotwired/stimulus';

/**
 * Controller attached to the confirmation modal window. Tells other controllers that it's time to
 * remove their collection item prepared for removal.
 */
export default class FormCollectionRemovePreparedController extends Controller
{
    removePreparedItem()
    {
        this.dispatch('removePreparedItem');
    }
}