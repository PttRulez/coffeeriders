<script lang="ts" setup="">
import { Bike } from '@/types';
import BikeForm from '@/pages/adminka/rent-bikes/BikeForm.vue';


const { bike } = defineProps<{ bike: Bike }>();

</script>

<template>
    <BikeForm :bike="bike"/>
</template>