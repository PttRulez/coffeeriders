<?php

namespace App\Enums;

enum BookingStatusEnum: string
{
    case Booked   = 'booked';
    case Prepaid  = 'prepaid';
    case Paid     = 'paid';
    case Finished = 'finished';
}