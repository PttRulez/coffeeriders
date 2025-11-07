<!-- components/form-elements/HourPicker.vue -->
<script setup lang="ts">
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue,
} from '@/components/shadecn/select'
import { computed } from 'vue'

type Props = {
  modelValue?: string
  minHour?: number
  maxHour?: number
}

const props = withDefaults(defineProps<Props>(), {
  minHour: 7,
  maxHour: 20,
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

// генерируем список часов
const hours = computed(() =>
  Array.from({ length: props.maxHour - props.minHour + 1 }, (_, i) => {
    const h = i + props.minHour
    return `${String(h).padStart(2, '0')}:00`
  }),
)
</script>

<template>
  <Select :modelValue="props.modelValue" @update:modelValue="emit('update:modelValue', $event)">
    <SelectTrigger class="p-6 w-full">
      <SelectValue placeholder="Выберите время" />
    </SelectTrigger>

    <SelectContent>
      <SelectItem v-for="hour in hours" :key="hour" :value="hour">
        {{ hour }}
      </SelectItem>
    </SelectContent>
  </Select>
</template>