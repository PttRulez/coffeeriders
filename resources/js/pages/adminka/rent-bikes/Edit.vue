<script lang="ts" setup="">
import { Bike } from '@/types/rent-bikes';
import BikeForm from '@/pages/adminka/rent-bikes/components/BikeForm.vue';


const { bike } = defineProps<{ bike: Bike }>();

</script>

<template>
    <BikeForm :bike="bike"/>
</template>