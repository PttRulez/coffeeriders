<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { AppPageProps } from '@/types';
import { RaceType } from '@/types/enums';
import { Race } from '@/types/races';
import { router, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ArrowRight,
    CalendarDays,
    Coffee,
    Globe,
    MapPin,
    SquareArrowOutUpRight,
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    races: Race[];
    year: number;
    prevYear: number | null;
    nextYear: number | null;
    selectedRaceTypes: RaceType[];
    selectedRaceRanks: string[];
    raceTimeFilter: 'all' | 'upcoming';
}>();

const page = usePage<AppPageProps>();
const user = page.props.auth.user;

const selectedRaceTypesValue = ref<RaceType[]>(props.selectedRaceTypes ?? []);
const selectedRaceRanksValue = ref<string[]>(props.selectedRaceRanks ?? []);
const raceTimeFilterValue = ref<'all' | 'upcoming'>(props.raceTimeFilter ?? 'all');

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const raceTypeLabels: Record<RaceType, string> = {
    [RaceType.Road]: 'Шоссе',
    [RaceType.MTB]: 'МТБ',
    [RaceType.Gravel]: 'Грэвел',
    [RaceType.Indoor]: 'Indoor',
    [RaceType.Track]: 'Трэк',
    [RaceType.Cyclocross]: 'Циклокросс',
};

const raceTypeOptions: Array<{ value: RaceType; label: string }> = [
    { value: RaceType.Road, label: raceTypeLabels[RaceType.Road] },
    { value: RaceType.MTB, label: raceTypeLabels[RaceType.MTB] },
    { value: RaceType.Gravel, label: raceTypeLabels[RaceType.Gravel] },
    { value: RaceType.Indoor, label: raceTypeLabels[RaceType.Indoor] },
    { value: RaceType.Track, label: raceTypeLabels[RaceType.Track] },
    { value: RaceType.Cyclocross, label: raceTypeLabels[RaceType.Cyclocross] },
];

const getRaceTypes = (race: Race): RaceType[] => race.race_types ?? [];
const rankOptions = ['1', '2', '3'] as const;

const isAllSelected = () => selectedRaceTypesValue.value.length === 0;

const calendarLink = (targetYear: number) => {
    const params: Record<string, number | string | RaceType[] | string[]> = { year: targetYear };
    if (selectedRaceTypesValue.value.length > 0) {
        params.race_types = selectedRaceTypesValue.value;
    }
    if (selectedRaceRanksValue.value.length > 0) {
        params.race_ranks = selectedRaceRanksValue.value;
    }
    params.race_time_filter = raceTimeFilterValue.value;

    return route('races.calendar', params);
};

const submitFilters = () => {
    const params: Record<string, number | string | RaceType[] | string[]> = { year: props.year };
    if (selectedRaceTypesValue.value.length > 0) {
        params.race_types = selectedRaceTypesValue.value;
    }
    if (selectedRaceRanksValue.value.length > 0) {
        params.race_ranks = selectedRaceRanksValue.value;
    }
    params.race_time_filter = raceTimeFilterValue.value;

    router.get(route('races.calendar', params), {}, { preserveScroll: true });
};

const toggleAll = () => {
    selectedRaceTypesValue.value = [];
    submitFilters();
};

const toggleRaceType = (raceType: RaceType) => {
    const exists = selectedRaceTypesValue.value.includes(raceType);

    if (exists) {
        selectedRaceTypesValue.value = selectedRaceTypesValue.value.filter(
            (type) => type !== raceType,
        );
    } else {
        selectedRaceTypesValue.value = [...selectedRaceTypesValue.value, raceType];
    }

    submitFilters();
};

const isRaceTypeSelected = (raceType: RaceType) => selectedRaceTypesValue.value.includes(raceType);

const toggleRaceRank = (rank: string) => {
    const exists = selectedRaceRanksValue.value.includes(rank);
    if (exists) {
        selectedRaceRanksValue.value = selectedRaceRanksValue.value.filter((it) => it !== rank);
    } else {
        selectedRaceRanksValue.value = [...selectedRaceRanksValue.value, rank];
    }

    submitFilters();
};

const isRaceRankSelected = (rank: string) => selectedRaceRanksValue.value.includes(rank);

const toggleRaceTimeFilter = (filter: 'all' | 'upcoming') => {
    raceTimeFilterValue.value = filter;
    submitFilters();
};

const participate = (raceId: number) => {
    router.post(route('races.participate', raceId), {}, { preserveScroll: true });
};
</script>

<template>
    <Head :title="`Календарь гонок ${props.year}`" />

    <div class="space-y-8">
        <div class="flex items-center justify-between gap-3">
            <Link
                v-if="props.prevYear"
                :href="calendarLink(props.prevYear)"
                class="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="size-4" />
                {{ props.prevYear }}
            </Link>
            <span v-else class="w-12"></span>

            <h1 class="text-center font-semibold md:text-3xl">
                Календарь гонок {{ props.year }} года
            </h1>

            <Link
                v-if="props.nextYear"
                :href="calendarLink(props.nextYear)"
                class="inline-flex items-center gap-1 text-right text-sm text-muted-foreground hover:text-foreground"
            >
                {{ props.nextYear }}
                <ArrowRight class="size-4" />
            </Link>
            <span v-else class="w-12"></span>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="toggleRaceTimeFilter('all')">
                <Badge
                    :variant="raceTimeFilterValue === 'all' ? 'default' : 'secondary'"
                    class="cursor-pointer"
                >
                    Все гонки
                </Badge>
            </button>
            <button type="button" @click="toggleRaceTimeFilter('upcoming')">
                <Badge
                    :variant="raceTimeFilterValue === 'upcoming' ? 'default' : 'secondary'"
                    class="cursor-pointer"
                >
                    Предстоящие
                </Badge>
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="toggleAll">
                <Badge :variant="isAllSelected() ? 'default' : 'secondary'" class="cursor-pointer">
                    Все
                </Badge>
            </button>

            <button
                v-for="option in raceTypeOptions"
                :key="option.value"
                type="button"
                @click="toggleRaceType(option.value)"
            >
                <Badge
                    :variant="isRaceTypeSelected(option.value) ? 'default' : 'secondary'"
                    class="cursor-pointer"
                >
                    {{ option.label }}
                </Badge>
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button
                v-for="rank in rankOptions"
                :key="rank"
                type="button"
                @click="toggleRaceRank(rank)"
            >
                <Badge
                    :variant="isRaceRankSelected(rank) ? 'default' : 'secondary'"
                    class="cursor-pointer"
                >
                    <span class="inline-flex items-center gap-1">
                        <Coffee
                            v-for="cupIndex in Number(rank)"
                            :key="`${rank}-${cupIndex}`"
                            class="size-4"
                        />
                    </span>
                </Badge>
            </button>
        </div>

        <div v-if="props.races.length === 0" class="rounded-xl border p-6 text-muted-foreground">
            В этом году пока нет опубликованных гонок.
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Card
                v-for="race in props.races"
                :key="race.id"
                class="group flex h-full flex-col overflow-hidden pt-0 transition hover:shadow-lg"
            >
                <div v-if="race.cover_img_url" class="aspect-video overflow-hidden">
                    <img
                        :src="race.cover_img_url"
                        :alt="race.name"
                        class="h-full w-full object-cover transition group-hover:scale-105"
                    />
                </div>

                <CardHeader>
                    <CardTitle class="line-clamp-2 leading-7 flex justify-center items-center gap-2">
                        {{ race.name }}
                        <span v-if="race.location" class="text-muted-foreground"
                            >({{ race.location }})</span
                        >
                        -
                        <div class="inline-flex items-center gap-1">
                            <Coffee
                                v-for="cupIndex in Number(race.rank)"
                                :key="`${race.id}-cup-${cupIndex}`"
                                class="size-4 text-amber-600"
                            />
                        </div>
                    </CardTitle>
                </CardHeader>

                <CardContent class="flex-1 space-y-3 text-sm">
                    <div class="flex justify-between gap-5 md:gap-10">
                        <p class="inline-flex shrink-0 items-center gap-2">
                            <CalendarDays class="size-4 text-muted-foreground" />
                            <span>{{ formatDate(race.date) }}</span>
                        </p>
                        <div class="inline-flex flex-wrap items-center gap-2">
                            <Badge
                                v-for="raceType in getRaceTypes(race)"
                                :key="`${race.id}-${raceType}`"
                                variant="outline"
                            >
                                {{ raceTypeLabels[raceType] }}
                            </Badge>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <p v-if="race.yandex_map_url" class="flex items-center gap-2">
                            <MapPin class="size-4 text-muted-foreground" />
                            <a
                                :href="race.yandex_map_url"
                                target="_blank"
                                class="text-blue-500 hover:underline"
                            >
                                Я.Карты
                            </a>
                        </p>
                        <p
                            v-if="!race.in_our_studio && race.registration_url"
                            class="flex items-center gap-2"
                        >
                            <SquareArrowOutUpRight class="size-4 text-muted-foreground" />
                            <a
                                :href="race.registration_url"
                                class="text-blue-500 hover:underline"
                                target="_blank"
                            >
                                Регистрация
                            </a>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p v-if="race.organizer_website_url" class="flex items-center gap-2">
                            <Globe class="size-4 text-muted-foreground" />
                            <a
                                :href="race.organizer_website_url"
                                class="text-blue-500 hover:underline"
                                target="_blank"
                            >
                                {{ race.organizer_name || 'Сайт организатора' }}
                            </a>
                        </p>
                    </div>
                </CardContent>

                <CardFooter>
                    <Button
                        v-if="user?.is_coffeerider"
                        type="button"
                        class="mr-auto"
                        :variant="race.is_participating ? 'destructive' : 'default'"
                        @click="participate(race.id)"
                    >
                        {{ race.is_participating ? 'Не буду участвовать' : 'Буду участвовать' }}
                    </Button>

                    <Link :href="route('races.show', race.id)" class="font-bold underline">
                        Подробнее
                    </Link>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
