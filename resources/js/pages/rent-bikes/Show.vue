<script setup lang="ts">
import BookingForm from '@/components/rent-bikes/BookingForm.vue';
import CarouselThumbs from '@/components/shared/CarouselThumbs.vue';
import Modal from '@/components/shared/Modal.vue';
import { Button } from '@/components/ui/button';
import { Role } from '@/types/enums';
import { Bike } from '@/types/rent-bikes';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({ inheritAttrs: false });

const { bike } = defineProps<{ bike: Bike }>();
const page = usePage();
const isAdmin = computed(() => page.props.auth.user?.role === Role.Admin);

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
            <Modal
                v-if="!isAdmin"
                v-model:open="bookingDialogOpen"
                :title="`Бронирование ${bike.name}`"
            >
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

            <Button v-else as-child>
                <Link :href="route('adminka.rent-bikes.bookings.create', { bike: bike.id })">
                    Создать бронь (админ)
                </Link>
            </Button>
        </div>
        <article class="prose prose-sm max-w-none" v-html="bike.full_description"></article>
    </div>
</template>
