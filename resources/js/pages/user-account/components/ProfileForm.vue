<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import PhoneInput from '@/components/form-elements/PhoneInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { Button } from '@/components/shadecn/button';

const page = usePage();
const { auth } = page.props;

const form = useForm({
    email: auth.user.email,
    name: auth.user.name,
    phone: auth.user.phone,
    telegram_username: auth.user.telegram_username,
});

const submit = () => {
    form.post(route('user-account.update-info'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="grid gap-6 md:max-w-sm">
        <FormInput
            field-name="email"
            type="email"
            placeholder="мыло"
            v-model="form.email"
            :error-message="form.errors.email"
        />

        <FormInput
            field-name="name"
            placeholder="Ваше имя"
            v-model="form.name"
            :error-message="form.errors.name"
        />

        <PhoneInput
            field-name="phone"
            placeholder="телефон"
            v-model="form.phone"
            :error-message="form.errors.phone"
        />

        <FormInput
            field-name="telegram_username"
            placeholder="telegram"
            v-model="form.telegram_username"
            :error-message="form.errors.telegram_username"
        />

        <Button type="submit" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            Сохранить данные
        </Button>
    </form>
</template>
