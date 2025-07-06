<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/shadecn/input';
import { Label } from '@/components/shadecn/label';
import { cn } from '@/lib/utils';

const model = defineModel();

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

const { class: className, errorMessage, fieldName, placeholder, type } = defineProps<Props>();
</script>

<template>
    <div class="grid gap-2">
        <div v-if="label" class="flex items-center justify-between">
            <Label :for="fieldName" class="text-lg">{{ label }}</Label>

            <slot name="additionToLabel" />
        </div>
        <Input
            :class="cn('p-6 text-xl', className)"
            v-model="model"
            :id="fieldName"
            :type="type ?? 'text'"
            v-bind="$attrs"
            :placeholder="placeholder"
            autocomplete="off"
        />
        <InputError :message="errorMessage" />
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