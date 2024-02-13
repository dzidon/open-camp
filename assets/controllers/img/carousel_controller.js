import { Controller } from '@hotwired/stimulus';

/**
 * Lets Bootstrap image carousel have image indicators.
 */
export default class CarouselController extends Controller
{
    static targets = ['carousel', 'indicator'];

    connect()
    {
        this.carousel = $(this.carouselTarget).carousel();
        this.carousel.on('slid.bs.carousel', (event) =>
        {
            const activeImageIndex = event.to;
            this.updateActiveIndicator(activeImageIndex);
        });

        const activeImageIndex = $('.carousel-item.active').index();
        this.updateActiveIndicator(activeImageIndex);
    }

    updateActiveIndicator(indicatorNumber)
    {
        this.indicatorTargets.forEach((element, index) =>
        {
            if (index === indicatorNumber)
            {
                $(element).removeClass('image-black-and-white');
            }
            else
            {
                $(element).addClass('image-black-and-white');
            }
        });
    }
}