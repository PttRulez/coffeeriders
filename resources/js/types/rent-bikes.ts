import { BikeCategory } from '@/types/enums';

export interface BikePrice {
    period: string;
    price: number;
}

export interface Bike {
    booked_dates: Array<string>;
    bookings: BikeBooking[];
    category: BikeCategory;
    full_description: string;
    id: number;
    images: any[];
    name: string;
    predoplata: number;
    prices: BikePrice[];
    short_description: string;
    title_img: string;
}

export interface BikeBooking {
    bike_id: number;
    bike: Bike;
    comment: string;
    customer_name: string;
    ends_at: string;
    id: number;
    phone: string;
    starts_at: string;
    telegram_username: string;
}
