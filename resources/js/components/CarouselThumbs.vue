<script setup lang="ts">
import { Card, CardContent } from '@/components/shadecn/card';
import type { CarouselApi } from '@/components/shadecn/carousel';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/shadecn/carousel';
import { UnwrapRefCarouselApi } from '@/components/shadecn/carousel/interface';
import { watchOnce } from '@vueuse/core';
import { ref } from 'vue';

type Props = {
    images: {
        id?: number;
        url: string;
        alt: string;
    }[];
};
const props = defineProps<Props>();

const emblaMainApi = ref<CarouselApi>();
const emblaThumbnailApi = ref<CarouselApi>();
const selectedIndex = ref(0);

function onSelect() {
    if (!emblaMainApi.value || !emblaThumbnailApi.value) return;
    selectedIndex.value = emblaMainApi.value.selectedScrollSnap();
    emblaThumbnailApi.value.scrollTo(emblaMainApi.value.selectedScrollSnap());
}

function onThumbClick(index: number) {
    if (!emblaMainApi.value || !emblaThumbnailApi.value) return;
    emblaMainApi.value.scrollTo(index);
}

watchOnce(emblaMainApi, (emblaMainApi) => {
    if (!emblaMainApi) return;

    onSelect();
    emblaMainApi.on('select', onSelect);
    emblaMainApi.on('reInit', onSelect);
});
</script>

<template>
    <div class="w-full sm:w-auto">
        <Carousel
            class="relative w-full"
            :opts="{
                align: 'start',
                loop: true,
            }"
            @init-api="(val: UnwrapRefCarouselApi) => (emblaMainApi = val)"
        >
            <CarouselContent>
                <CarouselItem v-for="(img, index) in props.images" :key="img.id ?? index">
                    <div class="p-1">
                        <Card class="py-1">
                            <CardContent class="px-1">
                                <div class="aspect-[4/3] w-full sm:aspect-[16/9]">
                                    <img
                                        :src="img.url"
                                        :alt="img.alt ?? ''"
                                        class="h-full w-full rounded-md object-cover"
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </CarouselItem>
            </CarouselContent>
            <CarouselPrevious />
            <CarouselNext />
        </Carousel>

        <Carousel
            class="relative w-full"
            @init-api="(val: UnwrapRefCarouselApi) => (emblaThumbnailApi = val)"
        >
            <CarouselContent class="ml-0 flex gap-1">
                <CarouselItem
                    v-for="(img, index) in props.images"
                    :key="index"
                    class="basis-1/4 cursor-pointer pl-0"
                    @click="onThumbClick(index)"
                >
                    <div class="p-1" :class="index === selectedIndex ? '' : 'opacity-50'">
                        <Card class="py-1">
                            <CardContent class="flex items-center justify-center px-1">
                                <img :src="img.url" :alt="img.alt" />
                            </CardContent>
                        </Card>
                    </div>
                </CarouselItem>
            </CarouselContent>
        </Carousel>
    </div>
</template>
