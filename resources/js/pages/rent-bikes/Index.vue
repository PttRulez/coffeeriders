<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/shadecn/card';
import { Bike } from '@/types';
import { BikeCategory } from '@/types/enums';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

type Props = {
    bikes: Bike[];
};

const { bikes } = defineProps<Props>();

const groupedBikes = computed(() => {
    return bikes.reduce(
        (acc, cur) => {
            if (!acc[cur.category]) {
                acc[cur.category] = [];
            }
            acc[cur.category].push(cur);
            return acc;
        },
        {} as Record<BikeCategory, Bike[]>,
    );
});

const getCategoryName = (name: BikeCategory): string => {
    switch (name) {
        case BikeCategory.Gravel:
            return 'Гравийники';
        case BikeCategory.Road:
            return 'Шоссеры';
        case BikeCategory.MTB:
            return 'МТБ';
        default:
            return 'Другие';
    }
};
</script>

<template>
    <Head title="Аренда чётких великов" />
    <template v-for="(bikes, categoryName) in groupedBikes" :key="categoryName">
        <h1 class="text-2xl text-center">{{ getCategoryName(categoryName) }}</h1>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-10 md:mb-20">
            <Card v-for="bike in bikes" :key="bike.id">
                <CardContent>
                    <img class="mx-auto h-40 md:h-50" :src="bike.img_url" alt="Specialized Crux" />
                </CardContent>
                <CardHeader>
                    <CardTitle>{{ bike.name }}</CardTitle>
                    <CardDescription>{{ bike.short_description }}</CardDescription>
                </CardHeader>

                <!--            <CardFooter> Card Footer</CardFooter>-->
            </Card>
        </div>
    </template>
</template>
