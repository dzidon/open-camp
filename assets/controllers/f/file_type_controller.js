import { Controller } from '@hotwired/stimulus';
import bsCustomFileInput from 'bs-custom-file-input';

/**
 * Initializes a file form type.
 */
export default class FileTypeController extends Controller
{
    connect()
    {
        const selector = '#' + $(this.element).attr('id');
        bsCustomFileInput.init(selector);
    }
}