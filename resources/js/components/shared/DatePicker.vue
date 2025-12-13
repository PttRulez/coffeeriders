<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import type { DateValue } from '@internationalized/date';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { ref, watchEffect } from 'vue';

const props = defineProps<{
    isDateDisabled?: (date: DateValue) => boolean;
    modelValue?: DateValue | null;
    placeholder?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: DateValue | null): void;
}>();

const internalDate = ref<DateValue | undefined>(undefined);
const open = ref(false);

watchEffect(() => {
    if (props.modelValue) {
        internalDate.value = props.modelValue;
    } else {
        internalDate.value = undefined;
    }
});

const handleSelect = (val: DateValue | undefined) => {
    if (val) {
        emit('update:modelValue', val as DateValue);
    } else {
        emit('update:modelValue', null);
    }
    open.value = false;
};

// утилита для форматирования (чтобы dd.MM.yyyy)
function formatDate(val: DateValue): string {
    return `${String(val.day).padStart(2, '0')}.${String(val.month).padStart(2, '0')}.${val.year}`;
}
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                class="justify-start p-6 text-left font-normal md:text-xl"
            >
                <span v-if="props.modelValue">
                    {{ formatDate(props.modelValue) }}
                </span>
                <span v-else class="pl-2 text-muted-foreground">
                    {{ placeholder ?? 'Выберите дату' }}
                </span>
                <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar
                v-model="internalDate"
                @update:model-value="handleSelect"
                weekday-format="short"
                locale="ru-RU"
                :is-date-disabled="props.isDateDisabled"
            />
        </PopoverContent>
    </Popover>
</template>
