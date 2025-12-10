import { User } from '@/types/index';

export type CyclingActivity = {
    can: {
        update: boolean
    };
    coupon_usage: CouponUsage;
    cycling_station?: CyclingStation;
    cycling_station_id: number;
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