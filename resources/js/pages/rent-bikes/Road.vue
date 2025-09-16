<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Bike } from '@/types';
import BikeCard from '@/components/BikeCard.vue';

type Props = {
    bikes: Bike[];
};

const { bikes } = defineProps<Props>();
</script>

<template>
    <Head title="Прокат шоссейных велосипедов в Санкт-Петербурге">
        <meta
            name="description"
            content="Шоссейные велосипеды напрокат в СПб – модели Giant, Canyon и другие.
                Подходят для гонок, тренировок и туров. Самовывоз, договор, без скрытых условий."
        />
        <meta name="keywords" content="аренда шоссейных велосипедов, аренда шоссейного велосипеда Санкт-Петербург,
            прокат шоссейных велосипедов СПб, шоссейный велосипед напрокат, взять шоссейный велосипед в аренду, велопрокат СПб,
            аренда велосипеда для шоссе, прокат гоночных велосипедов Санкт-Петербург">
    </Head>

    <h1 class="text-center">Аренда шоссейных велосипедов</h1>

    <div class="mb-10 grid grid-cols-1 gap-4 md:mb-20 md:grid-cols-3">
            <Link
                v-for="bike in bikes.sort((a, b) => b.prices[0].price - a.prices[0].price)"
                :key="bike.id"
                :href="route('rent-bikes.show', bike.id)"
            >
                <BikeCard :bike="bike" />
            </Link>
        </div>

    <div class="space-y-2">
    <p>Шоссейные велосипеды — это выбор для тех, кто ценит скорость, лёгкость и эффективность.
        Идеальный для асфальта, стартов и дальних маршрутов. Мы предлагаем в аренду <strong>шоссейные велосипеды в Санкт-Петербурге</strong>
        на топовых алюминиевых и карбоновых рамах, с оборудованием Shimano 105, Ultegra и выше.</p>

    <p>Подходят для:</p>
    <ul class="list-disc pl-10">
        <li>участия в гонках (Ironman, Gran Fondo)</li>
        <li>тренировок и катаний по шоссе</li>
        <li>дальних маршрутов по Ленобласти</li>
    </ul>

    <p>Преимущества:</p>
    <ul class="list-disc pl-10">
        <li>максимальная скорость и накат</li>
        <li>лёгкий вес и высокая жёсткость</li>
        <li>идеальны для асфальта и ровных дорог</li>
    </ul>
        </div>
</template>