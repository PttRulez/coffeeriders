<script lang="ts" setup="">
import RaceForm from '@/pages/adminka/races/components/RaceForm.vue';
import { Race } from '@/types/races';

const { race } = defineProps<{ race: Race }>();
</script>

<template>
    <RaceForm :race="race" />
</template>
