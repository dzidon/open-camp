// js
const $ = require('jquery');

import 'bootstrap';
import bsCustomFileInput from 'bs-custom-file-input';

import tinymce from 'tinymce/tinymce'
import 'tinymce/themes/silver';
import 'tinymce/icons/default';

// start the Stimulus application
import '../scripts/bootstrap';

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

    scrollToFirstError();
    openCollapsedContainers();
});

function scrollToFirstError()
{
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
}

function openCollapsedContainers()
{
    let sizingData = {
        xs: {
            containers: $('.show-collapsed'),
            minWidth: 0,
        },
        sm: {
            containers: $('.show-collapsed-sm'),
            minWidth: 576,
        },
        md: {
            containers: $('.show-collapsed-md'),
            minWidth: 768,
        },
        lg: {
            containers: $('.show-collapsed-lg'),
            minWidth: 992,
        },
        xl: {
            containers: $('.show-collapsed-xl'),
            minWidth: 1200,
        },
    };

    const windowWidth = $(document).width();

    $.each(sizingData, function (key, data)
    {
        if (windowWidth < data.minWidth)
        {
            return;
        }

        data.containers.addClass('show');
    });
}