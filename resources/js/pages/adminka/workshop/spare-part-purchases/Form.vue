<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import FormTextArea from '@/components/form-elements/FormTextArea.vue';
import { Button } from '@/components/ui/button';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

type WorkshopSparePartPurchase = {
    id: number;
    workshop_spare_part_id: number;
    quantity: number;
    purchase_price_rub: number;
    purchased_at: string;
    comment: string | null;
};

type WorkshopSparePart = {
    id: number;
    name: string;
    category: { id: number; name: string } | null;
};

const props = defineProps<{
    item: WorkshopSparePartPurchase | null;
    sparePart: WorkshopSparePart;
}>();
const isEdit = computed(() => Boolean(props.item?.id));

const today = new Date().toISOString().slice(0, 10);

const form = useForm({
    quantity: props.item?.quantity ?? 1,
    purchase_price_rub: props.item?.purchase_price_rub ?? 0,
    purchased_at: props.item?.purchased_at ?? today,
    comment: props.item?.comment ?? '',
});

const submit = () => {
    if (isEdit.value) {
        form.put(
            route('adminka.workshop.spare-parts.purchases.update', {
                workshopSparePart: props.sparePart.id,
                workshopSparePartPurchase: props.item!.id,
            }),
        );
        return;
    }

    form.post(
        route('adminka.workshop.spare-parts.purchases.store', {
            workshopSparePart: props.sparePart.id,
        }),
    );
};
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-xl font-semibold">
            {{ isEdit ? 'Редактирование закупки' : 'Добавление закупки' }}
        </h1>
        <p class="text-sm text-muted-foreground">
            Запчасть:
            <span class="font-medium text-foreground">
                {{ props.sparePart.category?.name ? `${props.sparePart.category.name} - ` : '' }}
                {{ props.sparePart.name }}
            </span>
        </p>

        <form @submit.prevent="submit" class="grid max-w-2xl gap-4 mx-auto">
            <FormInput
                field-name="purchased_at"
                v-model="form.purchased_at"
                type="date"
                label="Дата закупки"
                :error-message="form.errors.purchased_at"
            />

            <FormInput
                field-name="quantity"
                v-model.number="form.quantity"
                type="number"
                label="Количество"
                placeholder="Например: 20"
                :error-message="form.errors.quantity"
            />

            <FormInput
                field-name="purchase_price_rub"
                v-model.number="form.purchase_price_rub"
                type="number"
                label="Цена закупки за штуку, ₽"
                placeholder="Например: 900"
                :error-message="form.errors.purchase_price_rub"
            />

            <FormTextArea
                field-name="comment"
                v-model="form.comment"
                label="Комментарий (необязательно)"
                :rows="3"
                :error-message="form.errors.comment"
            />

            <div class="flex gap-3 pt-2">
                <Button type="submit" :disabled="form.processing">
                    {{ isEdit ? 'Сохранить' : 'Добавить' }}
                </Button>
                <Button as-child variant="outline">
                    <Link
                        :href="
                            route('adminka.workshop.spare-parts.show', {
                                workshopSparePart: props.sparePart.id,
                            })
                        "
                    >
                        Отмена
                    </Link>
                </Button>
            </div>
        </form>
    </div>
</template>
