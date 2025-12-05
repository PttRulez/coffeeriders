<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

const model = defineModel<any>();

type Props = {
    class?: string;
    errorMessage?: string;
    fieldName: string;
    label?: string;
    placeholder?: string;
    type?: string;
};

defineOptions({
    inheritAttrs: false,
});

const props = withDefaults(defineProps<Props>(), {
    type: 'text',
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

        <Input
            v-model="model"
            :id="props.fieldName"
            :name="props.fieldName"
            :type="props.type"
            :class="cn('p-6 text-xl', props.class)"
            :placeholder="props.placeholder"
            autocomplete="off"
            v-bind="$attrs"
        />

        <InputError class="text-xs!" :message="props.errorMessage" />
    </div>
</template>

<style scoped>
:deep(input[type='number'])::-webkit-outer-spin-button,
:deep(input[type='number'])::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

:deep(input[type='number']) {
    -moz-appearance: textfield;
}

:deep(input[type='file']) {
    padding: 5px;
}
</style>