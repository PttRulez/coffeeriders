<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import BookingsTable from '@/pages/adminka/rent-bikes/components/BookingsTable.vue';
import { BikeBooking } from '@/types/rent-bikes';
import { router } from '@inertiajs/vue3';

type Props = {
    bookings: BikeBooking[];
    archive: boolean;
};

const props = defineProps<Props>();

const toggleArchive = (checked: boolean | 'indeterminate') => {
    const showArchive = checked === true;

    router.get(route('adminka.rent-bikes.bookings.index'), showArchive ? { archive: 1 } : {}, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <h1>Бронирования великов</h1>

        <Label class="flex cursor-pointer items-center gap-2">
            <Checkbox :model-value="props.archive" @update:model-value="toggleArchive" />
            <span>Архив</span>
        </Label>
    </div>

    <div class="w-full overflow-x-auto">
        <BookingsTable :bookings="props.bookings" :showBikeName="true" />
    </div>
</template>
