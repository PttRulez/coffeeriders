<script lang="ts" setup>
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import BookingsTable from '@/pages/adminka/rent-bikes/components/BookingsTable.vue';
import { Bike } from '@/types/rent-bikes';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    bike: Bike;
    archive: boolean;
}>();

const toggleArchive = (checked: boolean | 'indeterminate') => {
    const showArchive = checked === true;

    router.get(
        route('adminka.rent-bikes.show', { bike: props.bike.id }),
        showArchive ? { archive: 1 } : {},
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};
</script>

<template>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <h1>{{ props.bike.name }}</h1>

        <Label class="flex cursor-pointer items-center gap-2">
            <Checkbox :model-value="props.archive" @update:model-value="toggleArchive" />
            <span>Архив</span>
        </Label>
    </div>

    <img :src="bike.primary_img_url" :alt="bike.name" class="mx-auto mb-10 md:max-w-[560px]" />
    <div class="w-full overflow-x-auto">
        <BookingsTable :bookings="bike.bookings" />
    </div>
</template>
