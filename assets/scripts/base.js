// js
const $ = require('jquery');

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// start the Stimulus application
import '../scripts/bootstrap';

$(document).ready(function ()
{
    scrollToTop();
    scrollToFirstError();
    openCollapsedContainers();
    openModals();
});

function scrollToTop()
{
    window.scrollTo(0, 0);
}

function scrollToFirstError()
{
    const firstError = document.getElementsByClassName('invalid-feedback')[0];

    if (firstError)
    {
        setTimeout(() => {
            firstError.scrollIntoView({
                behavior: 'auto',
                block: 'center',
                inline: 'center'
            });
        }, 200);
    }
}

function openCollapsedContainers()
{
    let sizingData =
    {
        xs:
        {
            containers: $('.show-collapsed'),
            minWidth: 0,
        },
        sm:
        {
            containers: $('.show-collapsed-sm'),
            minWidth: 576,
        },
        md:
        {
            containers: $('.show-collapsed-md'),
            minWidth: 768,
        },
        lg:
        {
            containers: $('.show-collapsed-lg'),
            minWidth: 992,
        },
        xl:
        {
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

function openModals()
{
    const modalToOpen = document.querySelector('.modal-open-on-page-load');

    if (modalToOpen)
    {
        const modal = new bootstrap.Modal(modalToOpen);
        modal.show();
    }
}