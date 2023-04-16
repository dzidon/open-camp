import { Controller } from '@hotwired/stimulus';

/**
 * Controller attached to buttons that add new items to form collections.
 */
export default class FormCollectionAddController extends Controller
{
    static values = {
        collectionName: String,
        formName: String,
    }

    addItem()
    {
        const calledBy = {
            button: this.collectionNameValue,
            form: this.formNameValue,
        };

        this.dispatch('addItem', {
            detail: { calledBy }
        });
    }
}