<script setup lang="ts">
import { Breadcrumb, BreadcrumbItem, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/shadecn/breadcrumb';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/shadecn/card';
import { getPriceStringWithSeparators } from '@/helpers/price';
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
        <h1 class="text-center text-2xl">{{ getCategoryName(categoryName) }}</h1>
        <div class="mb-10 grid grid-cols-1 gap-4 md:mb-20 md:grid-cols-3">
            <Link
                v-for="bike in bikes.sort((a, b) => b.prices[0].price - a.prices[0].price)"
                :key="bike.id"
                :href="route('rent-bikes.show', bike.id)"
            >
                <Card class="h-full">
                    <CardContent>
                        <img class="mx-auto h-40 md:h-50" :src="bike.img_url" alt="Specialized Crux" />
                    </CardContent>
                    <CardHeader>
                        <CardTitle>{{ bike.name }}</CardTitle>
                        <CardDescription>{{ bike.short_description }}</CardDescription>
                    </CardHeader>

                    <CardFooter>
                        <Breadcrumb>
                            <BreadcrumbList>
                                <template v-for="(p, i) in bike.prices" :key="i">
                                    <BreadcrumbItem>
                                        <BreadcrumbPage
                                            ><span class="font-bold text-purple-900">{{ getPriceStringWithSeparators(p.price) }}</span>
                                            <span class="text-muted-foreground">/ {{ p.period }}</span>
                                        </BreadcrumbPage>
                                    </BreadcrumbItem>
                                    <BreadcrumbSeparator v-if="i < bike.prices.length - 1" />
                                </template>
                            </BreadcrumbList>
                        </Breadcrumb>
                    </CardFooter>
                </Card>
            </Link>
        </div>
    </template>
</template>
