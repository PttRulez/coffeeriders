import { RaceType } from '@/types/enums';

export type Race = {
    id: number;
    name: string;
    description: string | null;
    race_types: RaceType[];
    in_our_studio: boolean;
    organizer_name: string | null;
    organizer_website_url: string | null;
    registration_url: string | null;
    yandex_map_url: string | null;
    cover_img_url: string | null;
    is_participating?: boolean;
    date: string;
    price: number;
    is_published: boolean;
    clusters?: RaceCluster[];
    participants?: Array<{
        id: number;
        name: string;
        avatar_url: string | null;
    }>;
};

export type RaceCluster = {
    id: number;
    race_id: number;
    name: string;
    start_time: string;
    duration_minutes: number;
    price: number;
    available_slots?: number;
    cycling_activities_count?: number;
    cycling_activities?: Array<{
        id: number;
        user?: { id: number; name: string };
    }>;
};
