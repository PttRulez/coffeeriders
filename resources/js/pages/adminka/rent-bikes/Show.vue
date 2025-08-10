<script lang="ts" setup="">
import { TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/shadecn/table';
import { Bike } from '@/types/rent-bikes';
import { parseISO, format } from 'date-fns';
import { ru } from 'date-fns/locale';

const props = defineProps<{ bike: Bike }>();
const formatDate = (date: string) => {
    return format(parseISO(date), 'd MMMM', { locale: ru });
};
</script>

<template>
    <h1>{{ props.bike.name }}</h1>

    <div class="w-full overflow-x-auto">
        <Table class="min-w-full">
            <TableHeader>
                <TableRow>
                    <TableHead>имя</TableHead>
                    <TableHead>телефон</TableHead>
                    <TableHead>телеграм</TableHead>
                    <TableHead>даты</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="booking in bike.bookings" :key="booking.id">
                    <TableCell class="font-medium">
                        {{ booking.customer_name }}
                    </TableCell>
                    <TableCell>
                        <a :href="`tel:${booking.phone}`" class="flex items-center">
                            {{ booking.phone }}
                        </a>
                    </TableCell>
                    <TableCell>
                        <a
                            v-if="booking.telegram_username"
                            :href="`https://t.me/${booking.telegram_username}`"
                            class="flex items-center"
                            target="_blank"
                        >
                            {{ booking.telegram_username }}
                        </a>
                    </TableCell>
                    <TableCell class="whitespace-nowrap">
                        <div class="flex flex-col md:flex-row md:gap-1">
                            <span>{{ formatDate(booking.starts_at) }}</span>
                            <span class="hidden md:inline">-</span>
                            <span>{{ formatDate(booking.ends_at) }}</span>
                        </div>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
