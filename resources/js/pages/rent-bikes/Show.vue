<script setup lang="ts">
import BookingForm from '@/components/rent-bikes/BookingForm.vue';
import { Button } from '@/components/shadecn/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/shadecn/dialog';
import { Bike } from '@/types/rent-bikes';
import { ref } from 'vue';
import ImgCarousel from '@/components/ImgCarousel.vue';

defineOptions({ inheritAttrs: false });

const { bike } = defineProps<{ bike: Bike }>();

const bookingDialogOpen = ref(false);

function onSuccess() {
    bookingDialogOpen.value = false;
}
</script>

<template>
    <div class="flow-root">
        <div class="flex flex-col items-center gap-5 mb-10 max-md:px-5 md:pr-10 md:float-left md:mr-10 md:mb-10 md:w-[720px]">
            <h1 class="text-center">{{ bike.name }}</h1>
            <ImgCarousel :images="bike.images" />
            <p class="text-sm text-muted-foreground">{{ bike.short_description }}</p>
            <Dialog v-model:open="bookingDialogOpen">
                <DialogTrigger as-child>
                    <Button>Забронировать</Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle class="text-center leading-relaxed max-md:px-5"
                            >Бронирование {{ bike.name }}
                        </DialogTitle>
                    </DialogHeader>
                    <BookingForm
                        :bike_id="bike.id"
                        :booked_dates="bike.booked_dates"
                        @success="onSuccess"
                    />
                </DialogContent>
            </Dialog>
        </div>
        <article class="prose prose-sm max-w-none" v-html="bike.full_description"></article>
    </div>
</template>
