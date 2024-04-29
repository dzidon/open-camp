import { Controller } from '@hotwired/stimulus';

/**
 * Controller for editable text content containers.
 */
export default class ContainerController extends Controller
{
    static targets = ['content'];

    static outlets = [ 'tc--modal' ];

    static values = {
        identifier: String,
        html: Boolean,
    }

    connect()
    {
        this.content = $(this.contentTarget);
    }

    getContentAsString()
    {
        return this.content.html();
    }

    openEditModal()
    {
        if (!this.hasTcModalOutlet)
        {
            return;
        }

        const content = this.getContentAsString();
        this.tcModalOutlet.open(this.identifierValue, this.htmlValue, content);
    }
}