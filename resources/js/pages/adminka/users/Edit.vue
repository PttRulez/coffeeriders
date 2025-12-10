<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import FormSelect from '@/components/form-elements/FormSelect.vue';
import { Card } from '@/components/ui/card';
import { User } from '@/types';
import { Pedals } from '@/types/enums';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

const { user } = defineProps<{ user: User }>();


const form = useForm({
    height: user.height,
    weight: user.weight,
    pedals: user.pedals,
});

const pedalOptions = [
    { value: Pedals.Shimano, label: 'Shimano' },
    { value: Pedals.Look, label: 'Look' },
];

const submit = () => {
    form.put(route('adminka.users.update', user.id));
};
</script>

<template>
    <Card class="p-5 w-fit mx-auto">
        <form @submit.prevent="submit" class="grid gap-6 md:max-w-sm">
            <h1>{{ user.name }}</h1>
            <FormInput
                type="number"
                field-name="height"
                placeholder="рост, см"
                v-model="form.height"
                :error-message="form.errors.height"
            />

            <FormInput
                type="number"
                field-name="weight"
                placeholder="вес, кг"
                v-model="form.weight"
                :error-message="form.errors.weight"
            />

            <FormSelect field-name="pedals" :options="pedalOptions" v-model="form.pedals" />

            <Button type="submit">Сохранить</Button>
        </form>
    </Card>
</template>
