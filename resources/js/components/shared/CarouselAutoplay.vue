<script setup lang="ts">
import { Card, CardContent } from '@/components/shadecn/card';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/shadecn/carousel';
import Autoplay from 'embla-carousel-autoplay';
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue'

type Props = {
    images: {
        id?: number;
        url: string;
        alt: string;
    }[];
    hideArrows?: boolean;
    class?: HTMLAttributes['class'];
};
const props = defineProps<Props>();

</script>

<template>
    <div class="w-full sm:w-auto">
        <Carousel
            :class="cn('relative w-full', props.class) "
            :plugins="[
                Autoplay({
                    delay: 4000,
                }),
            ]"
            :opts="{ loop: true }"
        >
            <CarouselContent>
                <CarouselItem v-for="(img, index) in props.images" :key="img.id ?? index">
                    <div class="p-1">
                        <Card class="py-1 ">
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
            <CarouselPrevious  v-if="!props.hideArrows" />
            <CarouselNext  v-if="!props.hideArrows"  />
        </Carousel>
    </div>
</template>

<style scoped>
@media (max-width: 767px) {
    [data-slot='carousel-next'] {
        right: 10px;
    }

    [data-slot='carousel-previous'] {
        left: 10px;
    }
}
</style>
