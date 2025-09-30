<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import { Button } from '@/components/shadecn/button';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    new_password: '',
    new_password_confirmation: '',
});

const submit = () => {
    form.post(route('user-account.update-password'), {
        onFinish: () => form.reset('new_password', 'new_password_confirmation'),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="grid gap-6 md:max-w-sm">
        <FormInput
            field-name="new_password"
            type="password"
            placeholder="пароль"
            autocomplete="off"
            v-model="form.new_password"
            :error-message="form.errors.new_password"
        />
        <FormInput
            field-name="new_password_confirmation"
            type="password"
            placeholder="повтор пароля"
            autocomplete="off"
            v-model="form.new_password_confirmation"
            :error-message="form.errors.new_password_confirmation"
        />

        <Button type="submit" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            Обновить пароль
        </Button>
    </form>
</template>
