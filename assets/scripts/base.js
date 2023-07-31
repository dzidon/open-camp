// js
const $ = require('jquery');

import tinymce from 'tinymce/tinymce'
import 'tinymce/themes/silver';
import 'tinymce/icons/default';

tinymce.init({
    selector: 'textarea.tinymce',
    language: document.documentElement.lang,
    base_url: '/build/tinymce',
    branding: false,
    promotion: false,
});

import 'bootstrap';

// start the Stimulus application
import '../scripts/bootstrap';