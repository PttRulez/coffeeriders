<script setup lang="ts">
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/shadecn/table';
import { BikeBooking } from '@/types/rent-bikes';
import { format, parseISO } from 'date-fns';
import { ru } from 'date-fns/locale';
import { Trash } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

type Props = {
    bookings: BikeBooking[];
    showBikeName?: boolean;
};
const props = defineProps<Props>();

const formatDate = (date: string) => {
    return format(parseISO(date), 'd MMMM', { locale: ru });
};

const deleteBooking = (id: number) => {
    router.delete(route('adminka.rent-bikes.booking.destroy', id), {
        onSuccess: () => {
            toast.success('Букинг удален');
            router.reload({ only: ['bookings'] });
        },
    });
};
</script>

<template>
    <Table class="min-w-max">
        <TableHeader>
            <TableRow>
                <TableHead v-if="showBikeName">велик</TableHead>
                <TableHead>даты</TableHead>
                <TableHead>бабки</TableHead>
                <TableHead>имя</TableHead>
                <TableHead>телефон</TableHead>
                <TableHead>телеграм</TableHead>
                <TableHead></TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="booking in props.bookings" :key="booking.id">
                <TableCell class="font-medium" v-if="showBikeName">
                    <Link :href="route('adminka.rent-bikes.show', { bike: booking.bike.id })">
                        {{ booking.bike.name }}
                    </Link>
                </TableCell>

                <TableCell class="whitespace-nowrap">
                    <div class="flex flex-col md:flex-row md:gap-1">
                        <span>{{ formatDate(booking.starts_at) }}</span>
                        <span class="hidden md:inline">-</span>
                        <span>{{ formatDate(booking.ends_at) }}</span>
                    </div>
                </TableCell>

                <TableCell class="font-medium">
                    {{ booking.paid_money }}
                </TableCell>

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
                <TableCell>
                    <Trash @click="deleteBooking(booking.id)" class="cursor-pointer" />
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
