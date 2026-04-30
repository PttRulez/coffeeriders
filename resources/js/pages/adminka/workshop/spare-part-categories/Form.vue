<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import { Button } from '@/components/ui/button';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

type WorkshopSparePartCategory = {
    id: number;
    name: string;
};

const props = defineProps<{ item: WorkshopSparePartCategory | null }>();
const isEdit = computed(() => Boolean(props.item?.id));

const form = useForm({
    name: props.item?.name ?? '',
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('adminka.workshop.spare-part-categories.update', props.item!.id));
        return;
    }

    form.post(route('adminka.workshop.spare-part-categories.store'));
};
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-xl font-semibold">
            {{ isEdit ? 'Редактирование категории запчастей' : 'Добавление категории запчастей' }}
        </h1>

        <form @submit.prevent="submit" class="grid max-w-2xl gap-4">
            <FormInput
                field-name="name"
                v-model="form.name"
                label="Название категории"
                :error-message="form.errors.name"
            />

            <div class="flex gap-3 pt-2">
                <Button type="submit" :disabled="form.processing">
                    {{ isEdit ? 'Сохранить' : 'Добавить' }}
                </Button>
                <Button as-child variant="outline">
                    <Link :href="route('adminka.workshop.spare-part-categories.index')">Отмена</Link>
                </Button>
            </div>
        </form>
    </div>
</template>
