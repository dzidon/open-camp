import { Controller } from '@hotwired/stimulus';
import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver';
import 'tinymce/icons/default';

/**
 * Initializes a Tinymce WYSIWYG editor.
 */
export default class TinymceController extends Controller
{
    connect()
    {
        const selector = '#' + $(this.element).attr('id');

        tinymce.init({
            selector: selector,
            language: document.documentElement.lang,
            base_url: '/build/tinymce',
            branding: false,
            promotion: false,
        });
    }
}