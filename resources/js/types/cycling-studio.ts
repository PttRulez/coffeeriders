import { User } from '@/types/index';

export type CyclingActivity = {
    coupon_usage: CouponUsage;
    cycling_station: CyclingStation;
    distance: number;
    id: number;
    is_paid: boolean;
    starts_at: string;
    ends_at: string;
    note?: string;
    user: User;
}

type CouponUsage = {
    applied_discount: number;
    coupon_code: string;
    final_price: number;
}

type CyclingStation = {
    id: number;
    is_zwift_bike: boolean;
    name: string;
}