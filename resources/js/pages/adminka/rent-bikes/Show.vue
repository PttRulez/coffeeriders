<script lang="ts" setup="">

import { Bike } from '@/types/rent-bikes';

import BookingsTable from '@/pages/adminka/components/BookingsTable.vue';

const props = defineProps<{ bike: Bike }>();

</script>

<template>
    <h1>{{ props.bike.name }}</h1>
    <img :src="bike.img_url" :alt="bike.name" class="mx-auto md:max-w-[560px]" />
    <div class="w-full overflow-x-auto">
        <BookingsTable :bookings="bike.bookings" />
    </div>
</template>
