<script setup lang="ts">
import { Button } from '@/components/shadecn/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/shadecn/table';
import { Bike } from '@/types';
import { SquarePen } from 'lucide-vue-next';

type Props = {
    bikes: Bike[];
};

const { bikes } = defineProps<Props>();

</script>

<template>
    <Button as-child >
        <Link :href="route('adminka.rent-bikes.create')" class="w-fit">
            Добавить вел
        </Link>
    </Button>

    <Table>
        <TableHeader>
            <TableRow>
                <TableHead> Название</TableHead>
                <TableHead class="hidden lg:table-cell">Описание</TableHead>
                <TableHead>Цена</TableHead>
                <TableHead class="text-right"></TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="bike in bikes" :key="bike.id">
                <TableCell class="font-medium">{{ bike.name }}</TableCell>
                <TableCell class="hidden lg:table-cell">{{ bike.short_description }}</TableCell>
                <TableCell class="flex flex-col gap-3">
                    <p v-for="p in bike.prices" :key="p.period">
                        <span class="font-bold">{{ new Intl.NumberFormat('ru-RU').format(p.price) }}</span> -
                        {{ p.period }}
                    </p>
                </TableCell>
                <TableCell class="text-right">
                    <Link :href="route('adminka.rent-bikes.edit', bike.id)">
                        <SquarePen />
                    </Link>
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
