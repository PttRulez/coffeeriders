<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';

const model = defineModel<any>();

type Props = {
    class?: string;
    errorMessage?: string;
    fieldName: string;
    label?: string;
    placeholder?: string;
    rows?: number;
    wrapperClass?: string;
};

defineOptions({
    inheritAttrs: false,
});

const props = withDefaults(defineProps<Props>(), {
    rows: 4,
});
</script>

<template>
    <div :class="cn('grid gap-2', wrapperClass)">
        <div v-if="props.label" class="flex items-center justify-between">
            <Label :for="props.fieldName" class="text-lg">
                {{ props.label }}
            </Label>

            <slot name="additionToLabel" />
        </div>

        <Textarea
            v-model="model"
            :id="props.fieldName"
            :name="props.fieldName"
            :rows="props.rows"
            :class="cn('p-6 py-2 text-xl', props.class)"
            :placeholder="props.placeholder"
            autocomplete="off"
            v-bind="$attrs"
        />

        <InputError class="text-xs!" :message="props.errorMessage" />
    </div>
</template>
