<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { AppPageProps } from '@/types';
import { Race, RaceCluster } from '@/types/races';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const { race } = defineProps<{ race: Race }>();

const page = usePage<AppPageProps>();
const user = page.props.auth.user;

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

    <div class="space-y-5">
        <h1 class="text-2xl font-bold">{{ race.name }}</h1>

        <p class="text-lg">{{ formatDate(race.date) }}</p>

        <div class="mt-8">
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
