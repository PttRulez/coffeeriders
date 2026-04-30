<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import FormSelect from '@/components/form-elements/FormSelect.vue';
import FormTextArea from '@/components/form-elements/FormTextArea.vue';
import { Button } from '@/components/ui/button';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

type WorkshopSparePart = {
    id: number;
    workshop_spare_part_category_id: number;
    name: string;
    comment: string | null;
    purchase_price_rub: number;
    quantity: number;
    sale_price_rub: number;
    photo_url: string | null;
};

type WorkshopSparePartCategory = {
    id: number;
    name: string;
};

const props = defineProps<{
    item: WorkshopSparePart | null;
    categories: WorkshopSparePartCategory[];
}>();
const isEdit = computed(() => Boolean(props.item?.id));
const categoryOptions = computed(() =>
    props.categories.map((category) => ({
        label: category.name,
        value: category.id,
    })),
);

const form = useForm({
    workshop_spare_part_category_id: props.item?.workshop_spare_part_category_id ?? undefined,
    name: props.item?.name ?? '',
    comment: props.item?.comment ?? '',
    sale_price_rub: props.item?.sale_price_rub ?? 0,
    photo: null as File | null,
    _method: isEdit.value ? 'PUT' : undefined,
});

const submit = () => {
    if (isEdit.value) {
        form.post(
            route('adminka.workshop.spare-parts.update', {
                workshopSparePart: props.item!.id,
            }),
            {
                forceFormData: true,
            },
        );
        return;
    }

    form.post(route('adminka.workshop.spare-parts.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-xl font-semibold">
            {{ isEdit ? 'Редактирование запчасти' : 'Добавление запчасти' }}
        </h1>

        <form @submit.prevent="submit" class="grid max-w-2xl gap-4 mx-auto">
            <FormSelect
                field-name="workshop_spare_part_category_id"
                v-model="form.workshop_spare_part_category_id"
                label="Категория"
                placeholder="Выберите категорию"
                :options="categoryOptions"
                :error-message="form.errors.workshop_spare_part_category_id"
            >
                <template #additionToLabel>
                    <Link
                        class="text-sm text-blue-500"
                        :href="route('adminka.workshop.spare-part-categories.create')"
                    >
                        + Новая категория
                    </Link>
                </template>
            </FormSelect>

            <FormInput
                field-name="name"
                v-model="form.name"
                label="Название запчасти"
                :error-message="form.errors.name"
            />

            <FormTextArea
                field-name="comment"
                v-model="form.comment"
                label="Комментарий (необязательно)"
                :error-message="form.errors.comment"
                :rows="4"
            />

            <FormInput
                field-name="sale_price_rub"
                v-model.number="form.sale_price_rub"
                type="number"
                label="Цена продажи, ₽"
                placeholder="Например: 1500"
                :error-message="form.errors.sale_price_rub"
            />

            <div class="grid gap-2">
                <FormInput
                    field-name="photo"
                    type="file"
                    accept="image/*"
                    :label="isEdit ? 'Заменить фото (необязательно)' : 'Фото (необязательно)'"
                    :error-message="form.errors.photo"
                    @change="
                        (e: Event) =>
                            (form.photo = (e.target as HTMLInputElement).files?.[0] ?? null)
                    "
                />

                <img
                    v-if="props.item?.photo_url && !form.photo"
                    :src="props.item.photo_url"
                    alt="Текущее фото запчасти"
                    class="h-32 w-32 rounded-lg object-cover"
                />
            </div>

            <div class="flex gap-3 pt-2">
                <Button type="submit" :disabled="form.processing">
                    {{ isEdit ? 'Сохранить' : 'Добавить' }}
                </Button>
                <Button as-child variant="outline">
                    <Link :href="route('adminka.workshop.spare-parts.index')">Отмена</Link>
                </Button>
            </div>
        </form>
    </div>
</template>
