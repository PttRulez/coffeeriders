<?php

namespace App\Dto;

class TinkoffInitPaymentDto
{
    public function __construct(
        public float $amount,
        public string $failUrl,
        public string $orderId,
        public string $successUrl,
        public ?string $description = null,
    ) {}

    public function toArray(): array
    {
        return [
            'Amount'      => intval($this->amount * 100),
            'OrderId'     => $this->orderId,
            'Description' => $this->description ?? 'Оплата заказа',
            'SuccessURL'  => $this->successUrl,
            'FailURL'     => $this->failUrl,
        ];
    }
}