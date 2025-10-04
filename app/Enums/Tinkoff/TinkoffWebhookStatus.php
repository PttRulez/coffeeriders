<?php

namespace App\Enums\Tinkoff;

enum TinkoffWebhookStatus: string
{
    case CONFIRMED = 'CONFIRMED';
    case AUTHORIZED = 'AUTHORIZED';
}