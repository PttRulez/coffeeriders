<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Race } from '@/types/races';
import { SquarePen, Trash } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

const { races } = defineProps<{ races: Race[] }>();

const deleteRace = (race: Race) => {
    if (confirm(`Удалить гонку "${race.name}"?`)) {
        router.delete(route('adminka.races.destroy', race.id));
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>
    <Button as-child>
        <Link :href="route('adminka.races.create')" class="w-fit"> Создать гонку </Link>
    </Button>

    <Table class="mt-5">
        <TableHeader>
            <TableRow>
                <TableHead>Название</TableHead>
                <TableHead>Дата</TableHead>
                <TableHead>Цена</TableHead>
                <TableHead>Статус</TableHead>
                <TableHead class="text-right"></TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="race in races" :key="race.id">
                <TableCell class="font-medium">
                    <Link :href="route('adminka.races.show', race.id)">
                        {{ race.name }}
                    </Link>
                </TableCell>
                <TableCell>{{ formatDate(race.date) }}</TableCell>
                <TableCell>{{ new Intl.NumberFormat('ru-RU').format(race.price) }} руб</TableCell>
                <TableCell>
                    <span v-if="race.is_published" class="text-green-600">Опубликована</span>
                    <span v-else class="text-gray-500">Черновик</span>
                </TableCell>
                <TableCell class="text-right">
                    <div class="flex justify-end gap-3">
                        <Link :href="route('adminka.races.edit', race.id)">
                            <SquarePen class="cursor-pointer" />
                        </Link>
                        <button @click="deleteRace(race)" class="cursor-pointer text-red-600">
                            <Trash />
                        </button>
                    </div>
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
