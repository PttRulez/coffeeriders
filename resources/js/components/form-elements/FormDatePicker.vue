<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';
import DatePicker from '@/components/shared/DatePicker.vue';
import { Label } from '@/components/ui/label';
import { CalendarDate, type DateValue } from '@internationalized/date';
import { computed } from 'vue';

const model = defineModel<string | undefined>();

type Props = {
    errorMessage?: string;
    fieldName: string;
    isDateDisabled?: (date: DateValue) => boolean;
    label?: string;
    placeholder?: string;
};

defineOptions({
    inheritAttrs: false,
});

const props = defineProps<Props>();

const parseDate = (dateStr?: string): DateValue | null => {
    if (!dateStr) return null;
    const [year, month, day] = dateStr.split('-').map(Number);
    return new CalendarDate(year, month, day);
};

const formatDate = (val: DateValue): string => {
    return `${val.year}-${String(val.month).padStart(2, '0')}-${String(val.day).padStart(2, '0')}`;
};

const internalValue = computed({
    get: () => parseDate(model.value),
    set: (val: DateValue | null) => {
        model.value = val ? formatDate(val) : undefined;
    },
});
</script>

<template>
    <div class="grid gap-2">
        <div v-if="props.label" class="flex items-center justify-between">
            <Label :for="props.fieldName" class="text-lg">
                {{ props.label }}
            </Label>

            <slot name="additionToLabel" />
        </div>

        <DatePicker
            v-model="internalValue"
            :placeholder="props.placeholder"
            :is-date-disabled="props.isDateDisabled"
            v-bind="$attrs"
        />

        <InputError class="text-xs!" :message="props.errorMessage" />
    </div>
</template>