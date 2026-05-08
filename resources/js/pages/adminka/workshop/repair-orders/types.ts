export type RepairOrderStatus = 'pending' | 'in_work' | 'finished';

export type RepairOrder = {
    id: number;
    bike_name: string;
    status: RepairOrderStatus | string | null;
    client_phone: string | null;
    client_telegram: string | null;
    mechanic: {
        id: number;
        name: string;
    } | null;
    services_count: number;
    total_price_rub: number | null;
    mechanic_income_rub: number | null;
    workshop_works_income_rub: number | null;
    workshop_spare_parts_income_rub: number | null;
    workshop_income_rub: number | null;
    photos: Array<{
        id: number;
        photo_url: string;
    }>;
    created_at: string;
};
