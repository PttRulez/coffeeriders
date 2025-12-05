<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';

type Props = {
    fieldName: string;
    label?: string;
};

const model = defineModel<any>();

const { fieldName, label } = defineProps<Props>();
</script>

<template>
    <div class="flex items-center justify-start gap-2">
        <Checkbox id="remember" v-model="model" :tabindex="3" class="cursor-pointer" />
        <div v-if="label" class="flex items-center justify-between">
            <Label :for="fieldName" class="text-sm">{{ label }}</Label>
        </div>
    </div>
</template>
