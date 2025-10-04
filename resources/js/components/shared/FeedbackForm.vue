<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import PhoneInput from '@/components/form-elements/PhoneInput.vue';
import { Card } from '@/components/shadecn/card';
import { useForm } from '@inertiajs/vue3';
import { Textarea } from '@/components/shadecn/textarea';
import { Button } from '@/components/shadecn/button';

const emit = defineEmits<{
    (e: 'success'): void;
}>();

const form = useForm({
    name: '',
    phone: '',
    message: '',
});

function submit(): void {
    form.post(route('feedback-form'), {
        onSuccess: () => emit('success'),
    });
}
</script>

<template>
    <Card class="px-5">
        <form @submit.prevent="submit" class="space-y-5">
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

            <Textarea placeholder="Текст сообщения" class="resize-none" v-model="form.message" />

            <Button type="submit" class="w-full p-7">Отправить</Button>
        </form>
    </Card>
</template>
