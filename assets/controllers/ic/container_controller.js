import { Controller } from '@hotwired/stimulus';

/**
 * Controller for editable image content containers.
 */
export default class ContainerController extends Controller
{
    static outlets = ['ic--modal'];

    static values = {
        identifier: String,
        url: String,
        alt: String,
    };

    openEditModal()
    {
        if (!this.hasIcModalOutlet)
        {
            return;
        }

        this.icModalOutlet.open(this.identifierValue, this.urlValue, this.altValue);
    }
}