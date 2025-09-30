<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import { Button } from '@/components/shadecn/button';
import { Card } from '@/components/shadecn/card';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Восстановление пароля" />

    <Card class=" p-10 max-w-lg mx-auto">
        <form @submit.prevent="submit" class="flex flex-col gap-5">
            <FormInput
                field-name="email"
                placeholder="ваш email"
                autocomplete="off"
                v-model="form.email"
                :error-message="form.errors.email"
            />

                <Button class="p-6" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Получить ссылку на email
                </Button>
        </form>

        <div class="space-x-1 text-center text-sm text-muted-foreground">
            <span>Вернуться на страницу</span>
            <Link :href="route('login')" class="text-blue-400">логина</Link>
        </div>
    </Card>
</template>
