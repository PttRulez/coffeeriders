<script setup lang="ts">

import { Bike } from '@/types';

const { bike } = defineProps<{ bike: Bike }>();

</script>

<template>
    <h1 class="text-center">{{ bike.name }}</h1>
    <img
        :src="bike.img_url"
        :alt="bike.name"
        class="max-w-[560px] mx-auto"
    />
    <article class="mt-6 prose prose-sm max-w-none" v-html="bike.full_description">
    </article>

</template>