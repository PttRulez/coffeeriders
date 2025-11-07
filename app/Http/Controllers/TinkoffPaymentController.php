<?php

namespace App\Http\Controllers;

use App\Enums\Tinkoff\TinkoffWebhookStatus;
use App\Models\BikeBooking;
use App\Models\CyclingOrder;
use App\Services\TinkoffService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class TinkoffPaymentController extends Controller
{
    public function __construct(private TinkoffService $tinkoffService)
    {
    }
    
    public function handleBikeBookingNotificationFromBank(BikeBooking $bikeBooking, Request $request)
    {
        $data = $request->all();
        
        Log::channel('payments')->info('Tinkoff callback', [
            'data' => $data,
            '$bikeBooking->id' => $bikeBooking->id,
            'token_valid' => $this->tinkoffService->checkToken($data),
        ]);
        
        if ($data['Status'] == TinkoffWebhookStatus::CONFIRMED->value && $request->get('Amount') > 0) {
            $bikeBooking->increment('paid_money', $request->get('Amount') / 100);
        }
        
        return response('OK', 200)
            ->header('Content-Type', 'text/plain');
    }
    
    public function handleCyclingNotificationFromBank(Request $request)
    {
       $data = $request->all();
        
        Log::channel('payments')->info('Tinkoff callback', [
            'data' => $data,
            'token_valid' => $this->tinkoffService->checkToken($data),
        ]);
        
        if ($data['Status'] == TinkoffWebhookStatus::CONFIRMED->value && $request->get('Amount') > 0) {
            $orderId = str_replace('cycling_order_', '', $request->input('OrderId'));
            $cyclingOrder = CyclingOrder::with('user')->find($orderId);
            $cyclingOrder->update([
                'amount_paid' => $request->get('Amount') / 100,
            ]);
            
            $cyclingOrder->user->increment('paid_cycling_count', $cyclingOrder->quantity);
        }
        
        return response('OK', 200)
            ->header('Content-Type', 'text/plain');
    }
    
    public function successPayment(Request $request): Response
    {
        Log::channel('payments')->info('Tinkoff redirected user to successPayment', [
            'request' => $request->all(),
        ]);
        
        return Inertia::render('SuccessPayment');
    }
    
    public function failedPayment(Request $request): Response
    {
       Log::channel('payments')->error('Tinkoff redirected user to failedPayment', [
            'request' => $request->all(),
        ]);
        
        return Inertia::render('FailedPayment');
    }
}