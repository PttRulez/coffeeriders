<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import FormSelect from '@/components/form-elements/FormSelect.vue';
import { Button } from '@/components/ui/button';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

type WorkshopService = {
    id: number;
    workshop_category_id: number;
    name: string;
    price_rub: number;
    additional_info: string | null;
};

type WorkshopCategory = {
    id: number;
    name: string;
};

const props = defineProps<{ item: WorkshopService | null; categories: WorkshopCategory[] }>();
const isEdit = computed(() => Boolean(props.item?.id));
const categoryOptions = computed(() =>
    props.categories.map((category) => ({
        label: category.name,
        value: category.id,
    })),
);

const form = useForm({
    workshop_category_id: props.item?.workshop_category_id ?? undefined,
    name: props.item?.name ?? '',
    price_rub: props.item?.price_rub ?? 0,
    additional_info: props.item?.additional_info ?? '',
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('adminka.workshop-services.update', props.item!.id));
        return;
    }

    form.post(route('adminka.workshop-services.store'));
};
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-xl font-semibold">
            {{ isEdit ? 'Редактирование услуги' : 'Добавление услуги' }}
        </h1>

        <form @submit.prevent="submit" class="grid max-w-2xl gap-4">
            <FormSelect
                field-name="workshop_category_id"
                v-model="form.workshop_category_id"
                label="Категория"
                placeholder="Выберите категорию"
                :options="categoryOptions"
                :error-message="form.errors.workshop_category_id"
            >
                <template #additionToLabel>
                    <Link
                        class="text-sm text-blue-500"
                        :href="route('adminka.workshop-categories.create')"
                    >
                        + Новая категория
                    </Link>
                </template>
            </FormSelect>

            <FormInput
                field-name="name"
                v-model="form.name"
                label="Название услуги"
                placeholder="Например: Настройка переключателей"
                :error-message="form.errors.name"
            />

            <FormInput
                field-name="price_rub"
                v-model.number="form.price_rub"
                type="number"
                label="Цена, ₽"
                placeholder="Например: 1500"
                :error-message="form.errors.price_rub"
            />

            <FormInput
                field-name="additional_info"
                v-model="form.additional_info"
                label="Доп. информация (необязательно)"
                placeholder="Например: за 1 колесо"
                :error-message="form.errors.additional_info"
            />

            <div class="flex gap-3 pt-2">
                <Button type="submit" :disabled="form.processing">
                    {{ isEdit ? 'Сохранить' : 'Добавить' }}
                </Button>
                <Button as-child variant="outline">
                    <Link :href="route('adminka.workshop-services.index')">Отмена</Link>
                </Button>
            </div>
        </form>
    </div>
</template>
