<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import { Button } from '@/components/ui/button';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

type WorkshopCategory = {
    id: number;
    name: string;
    sort_order: number;
};

const props = defineProps<{ item: WorkshopCategory | null }>();
const isEdit = computed(() => Boolean(props.item?.id));

const form = useForm({
    name: props.item?.name ?? '',
    sort_order: props.item?.sort_order ?? 0,
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('adminka.workshop-categories.update', props.item!.id));
        return;
    }

    form.post(route('adminka.workshop-categories.store'));
};
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-xl font-semibold">
            {{ isEdit ? 'Редактирование категории' : 'Добавление категории' }}
        </h1>

        <form @submit.prevent="submit" class="grid max-w-2xl gap-4">
            <FormInput
                field-name="name"
                v-model="form.name"
                label="Название категории"
                placeholder="Например: Техническое обслуживание"
                :error-message="form.errors.name"
            />

            <FormInput
                field-name="sort_order"
                v-model.number="form.sort_order"
                type="number"
                label="Порядок сортировки"
                placeholder="0"
                :error-message="form.errors.sort_order"
            />

            <div class="flex gap-3 pt-2">
                <Button type="submit" :disabled="form.processing">
                    {{ isEdit ? 'Сохранить' : 'Добавить' }}
                </Button>
                <Button as-child variant="outline">
                    <Link :href="route('adminka.workshop-categories.index')">Отмена</Link>
                </Button>
            </div>
        </form>
    </div>
</template>
