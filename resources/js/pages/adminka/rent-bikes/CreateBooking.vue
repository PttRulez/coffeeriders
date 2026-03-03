<script setup lang="ts">
import BookingForm from '@/components/rent-bikes/BookingForm.vue';

const { bike } = defineProps<{
    bike: {
        id: number;
        name: string;
        booked_dates: string[];
        predoplata: number;
    };
}>();
</script>

<template>
    <h1 class="mb-6">Создание бронирования: {{ bike.name }}</h1>
    <div class="rounded-lg border p-4 md:p-6">
        <BookingForm
            mode="admin"
            :bike_id="bike.id"
            :booked_dates="bike.booked_dates"
            :predoplata="bike.predoplata"
        />
    </div>
</template>
