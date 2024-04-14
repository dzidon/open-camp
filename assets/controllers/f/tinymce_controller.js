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
            plugins: ['image', 'link', 'emoticons', 'table'],
            block_formats: 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Preformatted=pre',
            image_class_list: [
                {title: 'img-fluid', value: 'img-fluid'},
            ],
        });
    }
}