<?php

namespace App\Services;

use AllowDynamicProperties;
use App\Dto\TinkoffInitPaymentDto;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use function route;

class TinkoffService
{
    private string $baseUrl;
    
    public function __construct()
    {
        $this->baseUrl = config('services.tinkoff.base_url', 'https://securepay.tinkoff.ru/v2');
    }
    
    public function initPayment(TinkoffInitPaymentDto $dto): array
    {
        $payload = $dto->toArray();
        $payload['NotificationURL'] = route('tinkoff.handle-notification-from-bank');
        $payload['TerminalKey'] = config('services.tinkoff.terminal_key');
        $payload['Token'] = $this->generateToken($payload);
        
        $publicIp = file_get_contents('https://api.ipify.org');
        Log::channel('payments')->info('Sending request to Tinkoff', [
            'base_url' => $this->baseUrl,
            '$payload' => $payload,
            'ip' => $publicIp,
        ]);
        
        
        $response = Http::baseUrl($this->baseUrl)->post('/Init', $payload);
        
        if ($response->successful()) {
            Log::channel('payments')->info('Tinkoff successful initPayment response', [
                '$response' => $response->json(),
                'token_valid' => $this->checkToken($response->json()),
            ]);
        } else {
            Log::channel('payments')->info('Tinkoff failed initPayment response', [
                '$response' => $response,
            ]);
        }
        
        
        return $response->json();
    }
    
    private function generateToken(array $params): string
    {
        unset($params['Token']);
        $params['Password'] = config('services.tinkoff.secret_key');
        ksort($params);
        
        return hash('sha256', implode('', $params));
    }
    
    public function checkToken(array $params): bool
    {
        if (!isset($params['Token'])) {
            return false;
        }
        
        
        
        $calculated = $this->generateToken($params);
        
        Log::channel('payments')->info('Tinkoff checkToken', [
            '$params' => $params,
            'calculated_token' => $calculated,
        ]);
        
        return hash_equals($calculated, $params['Token']);
    }
}