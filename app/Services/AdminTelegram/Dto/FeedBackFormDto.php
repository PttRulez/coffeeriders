<?php

namespace App\Services\AdminTelegram\Dto;

class FeedBackFormDto
{
    public function __construct(
        public string $name,
        public string $message,
        public string $phone,
    ){}
}