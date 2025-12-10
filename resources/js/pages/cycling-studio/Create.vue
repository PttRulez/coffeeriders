<script setup lang="ts">
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import ActionButton from '@/components/shared/ActionButton.vue';
import CyclingBookingForm from '@/pages/cycling-studio/components/CyclingBookingForm.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <CyclingBookingForm v-if="page.props.auth.user" />
    <Card v-else class="mx-auto max-w-[520px]">
        <CardContent>
            <p class="text-center">
                Только авторизованные пользователи могут забронировать занятие
            </p>
        </CardContent>
        <CardFooter class="justify-center gap-5">
            <ActionButton as-child :href="route('login')" text="Логин" />
            <ActionButton as-child :href="route('register')" text="Регистрация" />
        </CardFooter>
    </Card>
</template>
