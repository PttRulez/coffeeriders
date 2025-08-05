<script setup lang="ts">
import { Breadcrumb, BreadcrumbItem, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/shadecn/breadcrumb';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/shadecn/card';
import { Bike } from '@/types';
import { Head } from '@inertiajs/vue3';
import { getPriceStringWithSeparators } from '../../helpers/price';

type Props = {
    bikes: Bike[];
};

const { bikes } = defineProps<Props>();
</script>

<template>
    <Head title="Аренда гравийных велосипедов в Санкт-Петербурге">
        <meta
            name="description"
            content="Гравийные байки в аренду – идеально для путешествий, гравел-рейдов и поездок по Ленобласти.
            Прокат велосипедов у метро Звёздная. Онлайн-бронирование."
        />
        <meta name="keywords" content="аренда гравийного велосипеда, прокат гравийного велосипеда Санкт-Петербург,
            аренда gravel bike СПб, взять гравийный велосипед в аренду, гравийный велосипед напрокат,
            gravel bike rental Saint Petersburg, аренда гравийника СПб, прокат gravel велосипедов СПб,
            rent a gravel bike in Saint Petersburg">
    </Head>

    <h1 class="text-center">Аренда гравийного велосипеда</h1>

    <div class="mb-10 grid grid-cols-1 gap-4 md:mb-20 md:grid-cols-3">
        <Link v-for="bike in bikes.sort((a, b) => b.prices[0].price - a.prices[0].price)" :key="bike.id" :href="route('rent-bikes.show', bike.id)">
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

    <p><strong>Гравийные велосипеды в аренду</strong> — это универсальный вариант для тех, кто хочет исследовать всё: от асфальта до лесных троп.
        Они оснащены более широкими покрышками, дисковыми тормозами и комфортной посадкой. Отлично подходят как для путешествий, так и для gravel-гонок.</p>
    <h3>Подходят для:</h3>
    <ul class="list-disc pl-10">
        <li>катания по смешанным покрытиям</li>
        <li>поездок за город</li>
        <li>грейвел-гонок и стартов</li>
    </ul>

    <h3>Преимущества:</h3>
    <ul class="list-disc pl-10">
        <li>устойчивость и проходимость</li>
        <li>комфортная посадка</li>
        <li>подходят как новичкам, так и опытным райдерам</li>
    </ul>
</template>
