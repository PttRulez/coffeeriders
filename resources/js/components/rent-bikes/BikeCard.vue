<script lang="ts" setup="">
import { Bike } from '@/types';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Breadcrumb, BreadcrumbItem, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { getPriceStringWithSeparators } from '@/helpers';

const { bike } = defineProps<{
    bike: Bike;
}>();
</script>

<template>
    <Card class="h-full">
        <CardContent>
            <img class="mx-auto h-40 md:h-50" :src="bike.title_img" alt="Specialized Crux" />
        </CardContent>
        <CardHeader>
            <CardTitle>{{ bike.name }}</CardTitle>
            <CardDescription class="text-base!">{{ bike.short_description }}</CardDescription>
        </CardHeader>

        <CardFooter>
            <Breadcrumb>
                <BreadcrumbList>
                    <template v-for="(p, i) in bike.prices" :key="i">
                        <BreadcrumbItem class="text-base!">
                            <BreadcrumbPage
                                ><span class="font-bold text-purple-900">{{
                                    getPriceStringWithSeparators(p.price)
                                }}</span>
                                <span class="text-muted-foreground">/ {{ p.period }}</span>
                            </BreadcrumbPage>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator v-if="i < bike.prices.length - 1" />
                    </template>
                </BreadcrumbList>
            </Breadcrumb>
        </CardFooter>
    </Card>
</template>
