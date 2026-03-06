<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useInitials } from '@/composables/useInitials';
import { AppPageProps } from '@/types';
import { Race, RaceCluster } from '@/types/races';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Globe, MapPin, SquareArrowOutUpRight } from 'lucide-vue-next';
import { ref } from 'vue';

const { race } = defineProps<{ race: Race }>();

const page = usePage<AppPageProps>();
const user = page.props.auth.user;
const { getInitials } = useInitials();

const loading = ref(false);
const errors = ref<Record<string, string>>({});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const getAvailableSlots = (cluster: RaceCluster) => {
    const registered = cluster.cycling_activities_count ?? 0;
    return 4 - registered;
};

const register = (cluster: RaceCluster) => {
    loading.value = true;
    errors.value = {};

    router.post(
        route('races.register', { race: race.id, cluster: cluster.id }),
        {},
        {
            onError: (e) => {
                errors.value = e;
            },
            onFinish: () => {
                loading.value = false;
            },
        },
    );
};
</script>

<template>
    <Head :title="race.name" />

    <div class="max-w-[1280px] space-y-5">
        <h1 class="text-2xl font-bold">
            {{ race.name }}
            <span v-if="race.location" class="text-muted-foreground">({{ race.location }})</span>
        </h1>

        <img
            v-if="race.cover_img_url"
            :src="race.cover_img_url"
            :alt="race.name"
            class="mx-auto max-h-80 w-full max-w-xl rounded-lg object-cover"
        />

        <section class="flex max-md:flex-col px-5 md:mx-auto w-fit gap-5 md:gap-30 mt-10">
            <div class="space-y-3">
                <p class="text-lg">{{ formatDate(race.date) }}</p>
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
                <p v-if="race.organizer_website_url" class="flex items-center gap-2">
                    <Globe class="size-4 text-muted-foreground" />
                    <a
                        :href="race.organizer_website_url"
                        class="text-blue-500 hover:underline"
                        target="_blank"
                    >
                        Сайт организатора
                    </a>
                </p>
            </div>

            <Separator class="md:hidden" />

            <div>
                <h2 class="mb-4 text-xl font-semibold">Участники от CoffeeRiders</h2>

                <div
                    v-if="!race.participants || race.participants.length === 0"
                    class="rounded border p-4 text-muted-foreground"
                >
                    Пока никто не отметился.
                </div>

                <Table v-else>
                    <TableBody>
                        <TableRow v-for="participant in race.participants" :key="participant.id">
                            <TableCell>
                                <Avatar class="size-9">
                                    <AvatarImage
                                        v-if="participant.avatar_url"
                                        :src="participant.avatar_url"
                                        :alt="participant.name"
                                    />
                                    <AvatarFallback>
                                        {{ getInitials(participant.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </TableCell>
                            <TableCell class="text-xl">{{ participant.name }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </section>

        <div v-if="race.in_our_studio" class="mt-8">
            <h2 class="mb-4 text-xl font-semibold">Стартовые группы</h2>

            <InputError :message="errors.cluster" class="mb-4" />

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card v-for="cluster in race.clusters" :key="cluster.id">
                    <CardContent class="space-y-2 pt-4">
                        <p class="text-lg font-bold">{{ cluster.name }}</p>
                        <p>Старт: {{ cluster.start_time }}</p>
                        <p>Продолжительность: {{ cluster.duration_minutes }} мин</p>
                        <p>Цена: {{ new Intl.NumberFormat('ru-RU').format(cluster.price) }} руб</p>
                        <p>
                            Свободных мест:
                            <span
                                :class="
                                    getAvailableSlots(cluster) > 0
                                        ? 'text-green-600'
                                        : 'text-red-600'
                                "
                            >
                                {{ getAvailableSlots(cluster) }} / 4
                            </span>
                        </p>
                    </CardContent>
                    <CardFooter>
                        <template v-if="user">
                            <Button
                                @click="register(cluster)"
                                :disabled="loading || getAvailableSlots(cluster) === 0"
                                class="w-full"
                            >
                                <template v-if="getAvailableSlots(cluster) === 0">
                                    Мест нет
                                </template>
                                <template v-else> Зарегистрироваться </template>
                            </Button>
                        </template>
                        <template v-else>
                            <Button as-child class="w-full">
                                <Link :href="route('login')">Войти для регистрации</Link>
                            </Button>
                        </template>
                    </CardFooter>
                </Card>
            </div>
        </div>

        <div v-if="race.description" class="mt-5">
            <article class="prose prose-sm max-w-none" v-html="race.description"></article>
        </div>
    </div>
</template>
