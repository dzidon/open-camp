import { Controller } from '@hotwired/stimulus';

/**
 * Wraps a form collection. Can insert and remove items.
 */
export default class FormCollectionWrapperController extends Controller
{
    static values = {
        index: Number, default: 0,
        collectionName: String,
        formName: String,
    };

    static targets = ['fields', 'item'];

    connect()
    {
        this.indexValue = this.itemTargets.length;
        this.prototype = String(this.element.dataset.prototype);
        this.itemPreparedForRemoval = null;
    }

    addItem({ detail: { calledBy = null } })
    {
        if (calledBy === null || (calledBy.button !== this.collectionNameValue || calledBy.form !== this.formNameValue))
        {
            return;
        }

        const newField = this.prototype.replace(/__name__/g, this.indexValue);
        this.fieldsTarget.insertAdjacentHTML('beforeend', newField);
        this.indexValue++;
    }

    removeItem({ detail: { item } })
    {
        if (!this.itemTargets.includes(item))
        {
            return;
        }

        item.remove();

        $('#fc-removal-modal').modal('hide');
    }

    removePreparedItem()
    {
        if (this.itemPreparedForRemoval === null)
        {
            return;
        }

        const item = this.itemPreparedForRemoval;
        this.removeItem({ detail: { item } });
        this.resetPreparedItem();
    }

    prepareItemForRemoval({ detail: { item } })
    {
        if (!this.itemTargets.includes(item))
        {
            return;
        }

        this.itemPreparedForRemoval = item;
    }

    resetPreparedItem()
    {
        this.itemPreparedForRemoval = null;
    }
}