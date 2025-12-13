export type Race = {
    id: number;
    name: string;
    description: string | null;
    date: string;
    price: number;
    is_published: boolean;
    clusters?: RaceCluster[];
    created_at: string;
    updated_at: string;
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
