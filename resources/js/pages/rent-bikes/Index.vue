<script setup lang="ts">
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Head } from '@inertiajs/vue3';
import { Bike } from '@/types';

type Props = {
    bikes: Bike[];
}

const { bikes } = defineProps<Props>();
console.log('bikes', bikes);
</script>

<template>
    <Head title="Аренда чётких великов" />
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <Card v-for="bike in bikes">
            <CardContent>
                <img
                    class="mx-auto h-40 md:h-50"
                    :src="bike.img_url" alt="Specialized Crux" />
            </CardContent>
            <CardHeader>
                <CardTitle>{{ bike.name }}</CardTitle>
                <CardDescription>{{ bike.short_description }}</CardDescription>
            </CardHeader>

<!--            <CardFooter> Card Footer</CardFooter>-->
        </Card>
    </div>
</template>
