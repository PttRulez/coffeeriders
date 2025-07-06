<script setup lang="ts">
import { BikeCategory } from '@/types/enums';

interface BikeForm extends Record<string, any> {
    category: BikeCategory;
    name: string;
    img: any;
    short_description: string;

    foods: {
        name: string;
        weight: number | null;
        calories_per_100: number | null;
        calories: number;
    }[];
}
</script>

<template>

</template>