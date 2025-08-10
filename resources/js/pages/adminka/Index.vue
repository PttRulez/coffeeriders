<script setup lang="ts">
import BookingsTable from '@/pages/adminka/components/BookingsTable.vue';
import { BikeBooking } from '@/types/rent-bikes';

type Props = {
    bookings: BikeBooking[]
}

const props = defineProps<Props>()
</script>

<template>
    <h1>Жоская админка</h1>

    <div class="w-full overflow-x-auto" >
        <BookingsTable :bookings="props.bookings" :showBikeName="true"/>
    </div>
</template>