<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';

type Props = {
  fieldName?: string;
  label?: string;
};

const model = defineModel<boolean>();
const props = defineProps<Props>();

const emit = defineEmits<{
  change: [value: boolean];
}>();

const onUpdate = (value: boolean | 'indeterminate') => {
  const boolValue = value === true;
  model.value = boolValue;
  emit('change', boolValue);
};
</script>

<template>
  <div class="flex items-center justify-start gap-2">
    <Checkbox
      :id="props.fieldName"
      :model-value="model"
      @update:modelValue="onUpdate"
      class="cursor-pointer"
    />
    <Label v-if="props.label" :for="props.fieldName" class="text-sm">
      {{ props.label }}
    </Label>
  </div>
</template>