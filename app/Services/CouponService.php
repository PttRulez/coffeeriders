<?php

namespace App\Services;

use App\Enums\ServiceType;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class CouponService
{
    public function preview(string $code, User $user, ServiceType $service, int $price): array
    {
        if ($user->isCoffeeRider()) {
            return ['ok' => false, 'message' => 'Купоны не работают для коферайдеров'];
        }
        
        $coupon = Coupon::active()
            ->service($service->value)
            ->where('code', trim($code))
            ->first();
        
        if (!$coupon) {
            return ['ok' => false, 'message' => 'Купон не найден'];
        }
        
        if (!$coupon->isValidForUser($user->id)) {
            return ['ok' => false, 'message' => 'Купон недействителен или лимит исчерпан'];
        }
        
        $newPrice = $coupon->applyDiscount($price);
        
        return [
            'ok' => true,
            'new_price' => $newPrice,
            'discount' => $price - $newPrice,
        ];
    }
    
    public function apply(string $code, User $user, ServiceType $service, int $price, Model $orderable): array
    {
        return DB::transaction(function () use ($code, $user, $service, $price, $orderable) {
            $coupon = Coupon::active()
                ->service($service->value)
                ->where('code', trim($code))
                ->first();
            
            if (!$coupon || !$coupon->isValidForUser($user->id)) {
                return ['ok' => false, 'message' => 'Купон недействителен'];
            }
            
            $newPrice = $coupon->applyDiscount($price);
            $applied = $price - $newPrice;
            
            try {
                $usage = $coupon->usages()->create([
                    'user_id' => $user->id,
                    'orderable_type' => get_class($orderable),
                    'orderable_id' => $orderable->id,
                    'coupon_code' => $coupon->code,
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => $coupon->discount_value,
                    'base_price' => $price,
                    'applied_discount' => $applied,
                    'final_price' => $newPrice,
                ]);
            } catch (QueryException $e) {
                return ['ok' => false, 'message' => 'К этой покупке уже применён купон'];
            }
            
            return [
                'ok' => true,
                'new_price' => $newPrice,
                'discount' => $applied,
                'coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code,
                'usage_id' => $usage->id,
            ];
        });
    }
}