<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';

export interface SelectOption {
    label: string;
    value: any;
}

const model = defineModel<any>();

type Props = {
    class?: string;
    errorMessage?: string;
    fieldName: string;
    label?: string;
    options: Array<SelectOption>;
    placeholder?: string;
    type?: string;
};

defineOptions({
    inheritAttrs: false,
});

const { class: className, errorMessage, fieldName, placeholder } = defineProps<Props>();
</script>

<template>
    <div class="grid w-full gap-2">
        <div v-if="label" class="flex items-center justify-between">
            <Label :for="fieldName" class="text-lg">{{ label }}</Label>

            <slot name="additionToLabel" />
        </div>

        <Select
            v-model="model"
            :class="cn('w-full bg-red-200 p-6 text-xl', className)"
            v-bind="$attrs"
        >
            <SelectTrigger class="max-md:text-xl">
                <SelectValue :placeholder="placeholder" class="text-xl" />
            </SelectTrigger>
            <SelectContent>
                <SelectGroup>
                    <SelectItem
                        v-for="option in options"
                        :key="option.label"
                        :value="option.value"
                        class="text-xl"
                        >{{ option.label }}
                    </SelectItem>
                </SelectGroup>
            </SelectContent>
        </Select>
        <InputError :message="errorMessage" />
    </div>
</template>

<style scoped>
:deep(button[data-slot='select-trigger']) {
    width: 100%;
    padding: calc(var(--spacing) * 6);
}
</style>
