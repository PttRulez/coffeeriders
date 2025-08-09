import { BikeCategory } from '@/types/enums';

export interface BikePrice {
    period: string;
    price: number;
}

export interface Bike {
    category: BikeCategory;
    full_description: string;
    id: number;
    img_url: string;
    name: string;
    prices: BikePrice[];
    short_description: string;
}
