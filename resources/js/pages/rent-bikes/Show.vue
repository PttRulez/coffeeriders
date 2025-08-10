<script setup lang="ts">
import { Button } from '@/components/shadecn/button';
import { Dialog, DialogHeader, DialogTitle, DialogTrigger, DialogContent } from '@/components/shadecn/dialog';
import { Bike } from '@/types/rent-bikes';
import BookingForm from '@/components/rent-bikes/BookingForm.vue';
import { ref } from 'vue';

defineOptions({ inheritAttrs: false })

const { bike } = defineProps<{ bike: Bike }>();

const bookingDialogOpen = ref(false)

function onSuccess() {
  bookingDialogOpen.value = false
}
</script>

<template>
    <div class="mb-10 flex flex-col items-center gap-10">
        <h1 class="text-center">{{ bike.name }}</h1>
        <img :src="bike.img_url" :alt="bike.name" class="mx-auto md:max-w-[560px]" />
        <Dialog v-model:open="bookingDialogOpen">
            <DialogTrigger as-child>
                <Button variant="secondary">Забронировать</Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle class="text-center max-md:px-5 leading-relaxed">Бронирование {{ bike.name }}</DialogTitle>
                </DialogHeader>
                <BookingForm :bike_id="bike.id" :booked_dates="bike.booked_dates" @success="onSuccess"/>
            </DialogContent>
        </Dialog>
    </div>
    <p class="text-sm text-muted-foreground">{{ bike.short_description }}</p>
    <article class="prose prose-sm mt-6 max-w-none" v-html="bike.full_description"></article>
</template>
