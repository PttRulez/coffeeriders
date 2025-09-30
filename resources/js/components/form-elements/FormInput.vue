<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';
import { Input } from '@/components/shadecn/input';
import { Label } from '@/components/shadecn/label';
import { Textarea } from '@/components/shadecn/textarea';
import { cn } from '@/lib/utils';

const model = defineModel<any>();

type Props = {
    class?: string;
    errorMessage?: string;
    fieldName: string;
    label?: string;
    multiline?: boolean;
    placeholder?: string;
    type?: string;
};

defineOptions({
    inheritAttrs: false,
});
const props = withDefaults(defineProps<Props>(), {
    multiline: false,
});
</script>

<template>
    <div class="grid gap-2">
        <div v-if="props.label" class="flex items-center justify-between">
            <Label :for="props.fieldName" class="text-lg">{{ props.label }}</Label>

            <slot name="additionToLabel" />
        </div>
        <component
            :is="props.multiline ? Textarea : Input"
            :class="cn({'p-6 text-xl': true, [props.class]: !! props.class, 'py-2': props.multiline})"
            v-model="model"
            :id="props.fieldName"
            :type="props.type ?? 'text'"
            v-bind="$attrs"
            :placeholder="props.placeholder"
            autocomplete="off"
        />
        <InputError class="text-xs!" :message="props.errorMessage" />
    </div>
</template>

<style scoped>
/* use deep selector if necessary */
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
