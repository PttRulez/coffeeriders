<script setup lang="ts">
import { Bike } from '@/types';
import { Head } from '@inertiajs/vue3';
import BikeCard from '@/components/rent-bikes/BikeCard.vue';

type Props = {
    bikes: Bike[];
};

const { bikes } = defineProps<Props>();
</script>

<template>
    <Head title="Прокат горных (МТБ) велосипедов в СПб">
        <meta name="description" content="Прокат горных и MTB велосипедов в Санкт-Петербурге — аренда на сутки или дольше.
            Катайтесь по лесу, паркам и пересечёнке. В наличии шлемы, фляги и другие аксессуары.">
        <meta name="keywords" content="аренда горного велосипеда, прокат горного велосипеда СПб,
            аренда MTB велосипеда Санкт-Петербург, прокат MTB bike, горный велосипед напрокат, велосипед для леса,
            аренда велосипеда для бездорожья, mountain bike rental Saint Petersburg, взять горный велосипед в аренду,
            rent a mountain bike in SPb">
    </Head>

    <h1 class="text-center">Аренда горного (МТБ) велосипеда</h1>

    <div class="mb-10 grid grid-cols-1 gap-4 md:mb-20 md:grid-cols-3">
        <Link v-for="bike in bikes.sort((a, b) => b.prices[0].price - a.prices[0].price)" :key="bike.id" :href="route('rent-bikes.show', bike.id)">
            <BikeCard :bike="bike" />
        </Link>
    </div>

    <p>Если вы хотите кататься по тропам, паркам и пересечённой местности — <strong>аренда горного велосипеда в
        Санкт-Петербурге</strong> будет лучшим решением. В нашем прокате только надёжные MTB с амортизацией, дисковыми
        тормозами и прочными компонентами.</p>

    <h3>Подходят для:</h3>
    <ul class="list-disc pl-10">
        <li>катания по бездорожью и паркам</li>
        <li>выездов в лес, трейлы, грунтовки</li>
        <li>активных выходных с друзьями</li>
    </ul>

    <h3>Преимущества:</h3>
    <ul class="list-disc pl-10">
        <li>амортизация и комфорт на кочках</li>
        <li>устойчивость и контроль</li>
        <li>прочные колёса и надёжные компоненты</li>
    </ul>
</template>
