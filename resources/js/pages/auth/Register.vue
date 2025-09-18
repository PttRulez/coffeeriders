<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import FormInput from '@/components/form/FormInput.vue';
import PhoneInput from '@/components/form/PhoneInput.vue';
import { Button } from '@/components/shadecn/button';
import { Card } from '@/components/shadecn/card';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    telegram_username: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register');
};
</script>

<template>
    <Head title="регистрация" />

    <Card class="mx-auto max-w-lg p-5 md:p-10">
        <h1>Регистрация</h1>
        <form @submit.prevent="submit" class="mx-auto flex flex-col gap-6">
            <FormInput
                field-name="name"
                placeholder="Ваше имя"
                v-model="form.name"
                :error-message="form.errors.name"
            />

            <FormInput
                field-name="email"
                type="email"
                placeholder="мыло"
                v-model="form.email"
                :error-message="form.errors.email"
            />

            <p class="text-sm!">Нужно как минимум что-то одно (телефон или телега), а лучще оба.</p>
            <FormInput
                field-name="telegram_username"
                placeholder="telegram"
                v-model="form.telegram_username"
                :error-message="form.errors.telegram_username"
            />

            <PhoneInput
                field-name="phone"
                placeholder="телефон"
                v-model="form.phone"
                :error-message="form.errors.phone"
            />

            <FormInput
                field-name="password"
                type="password"
                placeholder="пароль"
                autocomplete="off"
                v-model="form.password"
                :error-message="form.errors.password"
            />
            <FormInput
                field-name="password_confirmation"
                type="password"
                placeholder="повтор пароля"
                autocomplete="off"
                v-model="form.password_confirmation"
                :error-message="form.errors.password_confirmation"
            />

            <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Создать аккаунт
            </Button>

            <div class="text-center text-sm text-muted-foreground">
                Уже есть аккаунт?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6"
                    >Войти
                </TextLink>
            </div>
        </form>
    </Card>
</template>
