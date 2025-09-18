<script setup lang="ts">
import CyclingBookingForm from '@/components/cycling-studio/CyclingBookingForm.vue';
import { Card, CardContent, CardFooter } from '@/components/shadecn/card';
import { usePage } from '@inertiajs/vue3';
import ActionButton from '@/components/ActionButton.vue';

const page = usePage();
</script>

<template>
    <CyclingBookingForm v-if="page.props.auth.user" />
    <Card v-else class="max-w-[520px] mx-auto">
        <CardContent>
            <p class="text-center">Только авторизованные пользователи могут забронировать занятие</p>
        </CardContent>
        <CardFooter class="gap-5 justify-center">
            <ActionButton as-child :href="route('login')" text="Логин" />
            <ActionButton as-child :href="route('register')" text="Регистрация" />
        </CardFooter>
    </Card>
</template>
