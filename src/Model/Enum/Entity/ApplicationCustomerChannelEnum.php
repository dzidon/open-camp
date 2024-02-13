<?php

namespace App\Model\Enum\Entity;

/**
 * Customer channel (Facebook, Instagram, Ad, ...).
 */
enum ApplicationCustomerChannelEnum: string
{
    case FACEBOOK = 'facebook';
    case INSTAGRAM = 'instagram';
    case TIKTOK = 'tiktok';
    case INTERNET_AD = 'internet_ad';
    case WORD_OF_MOUTH = 'word_of_mouth';
    case OTHER = 'other';
}
