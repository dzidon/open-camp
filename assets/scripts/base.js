// js
const $ = require('jquery');

import 'bootstrap';
import bsCustomFileInput from 'bs-custom-file-input';

import tinymce from 'tinymce/tinymce'
import 'tinymce/themes/silver';
import 'tinymce/icons/default';

$(document).ready(function ()
{
    // file browser
    bsCustomFileInput.init();

    // tinymce
    tinymce.init({
        selector: 'textarea.tinymce',
        language: document.documentElement.lang,
        base_url: '/build/tinymce',
        branding: false,
        promotion: false,
    });

    // scroll to first error on page
    const firstError = document.getElementsByClassName('form-error-message')[0];
    if (firstError)
    {
        setTimeout(() => {
            firstError.scrollIntoView({
                behavior: 'auto',
                block: 'center',
                inline: 'center'
            });
        }, 250);
    }
});

// start the Stimulus application
import '../scripts/bootstrap';