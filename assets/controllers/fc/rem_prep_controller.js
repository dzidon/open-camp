import { Controller } from '@hotwired/stimulus';

/**
 * Controller attached to a form collection item removal button. Prepares an item for future removal.
 */
export default class FormCollectionPrepareRemovalController extends Controller
{
    static targets = ['button'];

    prepareItemForRemoval()
    {
        const item = this.getItem();

        this.dispatch('resetPreparedItem');
        this.dispatch('prepareItemForRemoval', {
            detail: { item },
        });
    }

    getItem()
    {
        return this.buttonTarget.closest('[data-fc--wrap-target="item"]');
    }
}