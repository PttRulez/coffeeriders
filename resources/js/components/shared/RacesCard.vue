<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardTitle } from '@/components/ui/card';
import { Race } from '@/types/races';

const { races } = defineProps<{ races: Race[] }>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
    });
};
</script>

<template>
    <Card v-for="race in races" :key="race.id" class="w-fit bg-amber-200" v-bind="$attrs">
        <CardTitle>Ближайшие гонки</CardTitle>
        <CardContent class="space-y-3 pt-4">
            <div>
                <div class="flex gap-2 items-center">
                    <p class="text-xl font-bold">{{ race.name }}</p>
                    <p class="text-lg">({{ formatDate(race.date) }})</p>
                    <Button as-child class="bg-red-300 text-blue-700" variant="outline">
                        <Link
                            :href="route('races.show', race.id)"
                            class="flex gap-5 hover:text-blue-500"
                        >
                            Регистрация
                        </Link>
                    </Button>
                </div>
            </div>
            <Button as-child class="w-full"> </Button>
        </CardContent>
    </Card>
</template>
