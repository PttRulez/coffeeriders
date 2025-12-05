<script setup lang="ts">
import CarouselThumbs from '@/components/shared/CarouselThumbs.vue';
import Modal from '@/components/shared/Modal.vue';
import BookingForm from '@/components/rent-bikes/BookingForm.vue';
import { Button } from '@/components/ui/button';
import { Bike } from '@/types/rent-bikes';
import { ref } from 'vue';

defineOptions({ inheritAttrs: false });

const { bike } = defineProps<{ bike: Bike }>();

const bookingDialogOpen = ref(false);

function onSuccess() {
    bookingDialogOpen.value = false;
}

</script>

<template>
    <div class="flow-root">
        <div
            class="mb-10 flex flex-col items-center gap-5 max-md:px-5 md:float-left md:mr-10 md:mb-10 md:w-[720px] md:pr-10"
        >
            <h1 class="text-center">{{ bike.name }}</h1>
            <CarouselThumbs :images="bike.images" />
            <p class="text-sm text-muted-foreground">{{ bike.short_description }}</p>
             <Modal v-model:open="bookingDialogOpen" :title="`Бронирование ${bike.name}`">
                <template #trigger>
                    <Button>Забронировать</Button>
                </template>

                <BookingForm
                    :bike_id="bike.id"
                    :booked_dates="bike.booked_dates"
                    :predoplata="bike.predoplata"
                    @success="onSuccess"
                />
            </Modal>
        </div>
        <article class="prose prose-sm max-w-none" v-html="bike.full_description"></article>
    </div>
</template>
