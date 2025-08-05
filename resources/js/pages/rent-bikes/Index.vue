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
    <Head title="Аренда велосипедов в СПб – Шоссейные, Гравийные, МТБ">
        <meta
            name="description"
            content="Возьми велосипед напрокат в Санкт-Петербурге: шоссейные, гравийные и МТБ модели. Прокат от 2500₽ в день. Забирай у метро Удельная — без лишних заморочек."
        />
        <meta name="keywords" content="аренда велосипедов спб, прокат шоссейных велосипедов, гравийные велосипеды напрокат" />
    </Head>
    <h1 class="text-center">Прокат велосипедов в Санкт-Петербурге с прозрачным сервисом</h1>
    <template v-for="(bikes, categoryName) in groupedBikes" :key="categoryName">
        <h2 class="text-center text-2xl">
            <Link :href="route('rent-bikes.category', { categoryName })">
                {{ getCategoryName(categoryName) }}
            </Link>
        </h2>

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

    <p>
        Coffeeriders предлагает <strong>аренду велосипедов в Санкт-Петербурге с договором</strong> — прозрачно, оперативно и по-человечески. Мы быстро
        отвечаем, не усложняем процесс и всегда на связи. Все модели проходят регулярное техническое обслуживание и укомплектованы всем необходимым.
    </p>
    <p>В прокате доступны:</p>
    <ul>
        <li>
            <strong>
                <Link :href="route('rent-bikes.category', { categoryName: BikeCategory.Road })" class="text-blue-400"> Шоссейные велосипеды </Link>
            </strong>
            — лёгкие, быстрые, идеально подходят для асфальта, стартов и тренировок;
        </li>
        <li>
            <strong>
                <Link :href="route('rent-bikes.category', { categoryName: BikeCategory.Gravel })" class="text-blue-400"> Гравийные велосипеды </Link>
            </strong>
            — универсальные и комфортные для поездок по смешанным покрытиям, велопрогулок и путешествий;
        </li>
        <li>
            <strong>
                <Link :href="route('rent-bikes.category', { categoryName: BikeCategory.MTB })" class="text-blue-400"> Горные (MTB) велосипеды </Link>
            </strong>
            — надёжные и амортизированные, подойдут для лесных троп и пересечённой местности.
        </li>
    </ul>
    <p>
        <strong>Все велосипеды отобраны действующими спортсменами</strong> — мастерами спорта и основателями нашей велокоманды. Мы доверяем им сами и
        уверенно предлагаем вам.
    </p>
    <p>
        Самовывоз — <strong>в районе метро Лесная</strong>, недалеко от центра Санкт-Петербурга. Удобно заехать на машине или добраться на велосипеде.
        Вы можете <strong>арендовать велосипед на день, выходные или неделю</strong>. Также доступны шлемы, фляги и крепления.
    </p>
    <p>
        Если вы готовитесь к гонке, планируете поездку за город или просто хотите провести активные выходные — Coffeeriders обеспечит вам качественный
        велосипед и понятный сервис
    </p>

    <h2>Как арендовать велосипед в Санкт-Петербурге</h2>
    <p>
        В Coffeeriders мы сделали аренду велосипедов простой и понятной. Ниже — пошаговая инструкция, как взять шоссейный, гравийный или горный
        велосипед в прокат.
    </p>

    <ol>
        <li>
            <h3 class="font-bold">1. Выберите велосипед</h3>
            <p>
                Ознакомьтесь с доступными моделями на этой странице. Мы сдаём в аренду только проверенные велосипеды: шоссейные, гравийные и MTB — в
                отличном состоянии, с понятной комплектацией.
            </p>
        </li>

        <li>
            <h3 class="font-bold">2. Свяжитесь с нами</h3>
            <p>Напишите нам в Telegram или позвоните по указанному номеру. Мы быстро отвечаем, подскажем подходящий размер и уточним детали.</p>
        </li>

        <li>
            <h3 class="font-bold">3. Приезжайте на самовывоз</h3>
            <p>
                Самовывоз осуществляется <strong>в районе метро Лесная</strong>, недалеко от центра Санкт-Петербурга. Мы оформим аренду через простой
                договор и передадим вам полностью готовый велосипед.
            </p>
        </li>

        <li>
            <h3 class="font-bold">4. Возвращаете — и всё</h3>
            <p>После окончания срока аренды вы возвращаете велосипед в оговорённое время. Никаких сложностей — всё по договорённости и честно.</p>
        </li>

        <p>Все велосипеды обслужены, смазаны, с исправными тормозами и трансмиссией. Мы сдаём в прокат только технику, которой доверяем сами.</p>
        <p>Мы заботимся о том, чтобы ваша поездка была безопасной и комфортной. Вместе с велосипедом вы можете получить:</p>
    </ol>
    <ul>
        <li><strong>Шлем</strong> — в нескольких размерах, бесплатно по запросу</li>
    </ul>
</template>
