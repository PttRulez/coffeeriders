<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Race } from '@/types/races';

const { race } = defineProps<{ race: Race }>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ race.name }}</h1>
            <Button as-child>
                <Link :href="route('adminka.races.edit', race.id)">Редактировать</Link>
            </Button>
        </div>

        <div class="space-y-2">
            <p><strong>Дата:</strong> {{ formatDate(race.date) }}</p>
            <p v-if="race.registration_url">
                <strong>Ссылка на регистрацию:</strong>
                <a
                    :href="race.registration_url"
                    class="text-blue-400 hover:underline"
                    target="_blank"
                >
                    {{ race.registration_url }}
                </a>
            </p>
            <p>
                <strong>Цена:</strong> {{ new Intl.NumberFormat('ru-RU').format(race.price) }} руб
            </p>
            <p>
                <strong>Статус:</strong>
                <span v-if="race.is_published" class="text-green-600">Опубликована</span>
                <span v-else class="text-gray-500">Черновик</span>
            </p>
        </div>

        <div v-if="race.description" class="mt-5">
            <h2 class="mb-2 text-xl font-semibold">Описание</h2>
            <article class="prose prose-sm max-w-none" v-html="race.description"></article>
        </div>

        <div v-if="race.cover_img_url" class="mt-5 max-w-sm">
            <h2 class="mb-2 text-xl font-semibold">Обложка</h2>
            <img
                :src="race.cover_img_url"
                :alt="race.name"
                class="h-auto w-full rounded-lg object-cover"
            />
        </div>

        <div class="mt-5">
            <h2 class="mb-3 text-xl font-semibold">Кластеры</h2>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Название</TableHead>
                        <TableHead>Время старта</TableHead>
                        <TableHead>Продолжительность</TableHead>
                        <TableHead>Цена</TableHead>
                        <TableHead>Участники</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="cluster in race.clusters" :key="cluster.id">
                        <TableCell class="font-medium">{{ cluster.name }}</TableCell>
                        <TableCell>{{ cluster.start_time }}</TableCell>
                        <TableCell>{{ cluster.duration_minutes }} мин</TableCell>
                        <TableCell
                            >{{
                                new Intl.NumberFormat('ru-RU').format(cluster.price)
                            }}
                            руб</TableCell
                        >
                        <TableCell>
                            <span v-if="cluster.cycling_activities?.length">
                                {{ cluster.cycling_activities.map((a) => a.user?.name).join(', ') }}
                            </span>
                            <span v-else class="text-gray-400">Нет регистраций</span>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
