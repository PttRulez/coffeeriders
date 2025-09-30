<?php

namespace App\Http\Controllers;

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
    
    public function initRentBikePayment(Request $request, TinkoffService $tinkoff)
    {
        $orderId = uniqid();
        $amount = $request->input('amount');
        
        $payment = $tinkoff->initPayment($orderId, $amount, "Оплата аренды велосипеда №{$orderId}");
        
        if (!empty($payment['PaymentURL'])) {
            return redirect($payment['PaymentURL']);
        }
        
        return back()->withErrors('Ошибка создания платежа');
    }
    
    public function handleNotificationFromBank(Request $request)
    {
        $data = $request->all();
        
        Log::channel('payments')->info('Tinkoff callback', [
            'data' => $data,
            'token_valid' => $this->tinkoffService->checkToken($data),
        ]);
        
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