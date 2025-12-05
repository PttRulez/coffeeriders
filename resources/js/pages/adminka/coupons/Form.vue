<script setup lang="ts">
import DatePicker from '@/components/shared/DatePicker.vue';
import { dateValueToIso } from '@/helpers';
import { Link, useForm } from '@inertiajs/vue3';
import type { DateValue } from '@internationalized/date';
import { parseDate } from '@internationalized/date';
import { computed, ref, watch } from 'vue';

import FormCheckBox from '@/components/form-elements/FormCheckBox.vue';
import FormInput from '@/components/form-elements/FormInput.vue';
import FormSelect from '@/components/form-elements/FormSelect.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';

const props = defineProps<{ item: any | null }>();
const isEdit = computed(() => Boolean(props.item?.id));

const form = useForm({
    code: props.item?.code ?? '',
    discount_type: props.item?.discount_type ?? 'percent',
    discount_value: props.item?.discount_value ?? 10,
    service_type: props.item?.service_type ?? 'cycling',
    is_active: props.item?.is_active ?? true,
    starts_at: props.item?.starts_at ?? '',
    ends_at: props.item?.ends_at ?? '',
    max_uses: props.item?.max_uses ?? null,
    max_uses_per_user: props.item?.max_uses_per_user ?? null,
});

// DatePickers
const selectedStart = ref<DateValue | null>(
    props.item?.starts_at ? parseDate(props.item?.starts_at) : null,
);
const selectedEnd = ref<DateValue | null>(
    props.item?.ends_at ? parseDate(props.item?.ends_at) : null,
);

watch(selectedStart, (val) => {
    form.starts_at = val ? dateValueToIso(val as DateValue) : '';
});

watch(selectedEnd, (val) => {
    form.ends_at = val ? dateValueToIso(val as DateValue) : '';
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('adminka.coupons.update', props.item!.id));
    } else {
        form.post(route('adminka.coupons.store'));
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <FormInput
            fieldName="code"
            v-model="form.code"
            label="Код"
            placeholder="промокод"
            :error-message="form.errors.code"
        />

        <FormSelect
            fieldName="discount_type"
            v-model="form.discount_type"
            label="Тип скидки"
            :options="[
                { label: 'Проценты', value: 'percent' },
                { label: 'Фиксированная сумма', value: 'fixed' },
            ]"
            :error-message="form.errors.discount_type"
        />

        <FormInput
            fieldName="discount_value"
            v-model.number="form.discount_value"
            type="number"
            label="Значение скидки"
            placeholder="Например: 10"
            :error-message="form.errors.discount_value"
        />

        <FormSelect
            fieldName="service_type"
            v-model="form.service_type"
            label="Услуга"
            :options="[
                { label: 'Сайклинг', value: 'cycling' },
                { label: 'Аренда велосипеда', value: 'bike_rent' },
            ]"
            :error-message="form.errors.service_type"
        />

        <div class="flex flex-col gap-2">
            <Label class="text-lg">Дата начала</Label>
            <DatePicker v-model="selectedStart" placeholder="необязательно" />
        </div>

        <div class="flex flex-col gap-2">
            <Label class="text-lg">Дата окончания</Label>
            <DatePicker v-model="selectedEnd" placeholder="необязательно" />
            <div v-if="form.errors.ends_at" class="text-xs text-red-500">
                {{ form.errors.ends_at }}
            </div>
        </div>

        <FormInput
            fieldName="max_uses"
            v-model.number="form.max_uses"
            type="number"
            label="Макс. использований (всего)"
            placeholder="необязательно"
        />

        <FormInput
            fieldName="max_uses_per_user"
            v-model.number="form.max_uses_per_user"
            type="number"
            label="Макс. на пользователя"
            placeholder="необязательно"
        />

        <FormCheckBox fieldName="is_active" v-model="form.is_active" label="Активен" />

        <div class="col-span-full flex gap-3 pt-2">
            <Button type="submit" :disabled="form.processing">
                {{ isEdit ? 'Сохранить' : 'Создать' }}
            </Button>
            <Link
                :href="route('adminka.coupons.index')"
                class="h-10 rounded border px-4 leading-10"
            >
                Отмена
            </Link>
        </div>
    </form>
</template>
