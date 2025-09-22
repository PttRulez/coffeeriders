<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import FormInput from '@/components/form/FormInput.vue';
import { Card } from '@/components/shadecn/card';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/shadecn/button';
import { LoaderCircle } from 'lucide-vue-next';
import FormCheckBox from '@/components/form/FormCheckBox.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Вход на сайт" />
    <Card class="mx-auto max-w-lg p-5 md:p-10">
        <h1>Логин</h1>
        <form @submit.prevent="submit" class="mx-auto flex flex-col gap-6">
            <FormInput
                field-name="email"
                type="email"
                placeholder="мыло"
                v-model="form.email"
                :error-message="form.errors.email"
            />

            <FormInput
                field-name="password"
                type="password"
                placeholder="пароль"
                autocomplete="off"
                v-model="form.password"
                :error-message="form.errors.password"
            />

            <FormCheckBox v-model="form.remember" field-name="remember" label="Запомнить меня" />

            <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Войти
            </Button>
        </form>

        <div class="text-center text-sm text-muted-foreground">
            Нет аккаунта?
            <TextLink :href="route('register')" :tabindex="5">Регистрация</TextLink>
        </div>
    </Card>
</template>
