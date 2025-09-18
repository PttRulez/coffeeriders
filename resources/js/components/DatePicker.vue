<script setup lang="ts">
import { ref, watchEffect } from 'vue'
import { format } from 'date-fns'
import { Calendar } from '@/components/shadecn/calendar'
import {
  Popover,
  PopoverTrigger,
  PopoverContent,
} from '@/components/shadecn/popover'
import { Button } from '@/components/shadecn/button'
import { DateValue, parseDate } from '@internationalized/date'
import { Calendar as CalendarIcon } from 'lucide-vue-next';

const props = defineProps<{
  modelValue?: Date | null
  placeholder?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: Date | null): void
}>()

const internalDate = ref<DateValue | undefined>(undefined)
const open = ref(false)

watchEffect(() => {
  if (props.modelValue) {
    internalDate.value = parseDate(format(props.modelValue, 'yyyy-MM-dd'))
  } else {
    internalDate.value = undefined
  }
})

const handleSelect = (val: DateValue | undefined) => {
  if (val) {
    emit('update:modelValue', new Date(val.year, val.month - 1, val.day))
  } else {
    emit('update:modelValue', null)
  }
  open.value = false
}
</script>

<template>
  <Popover v-model:open="open">
    <PopoverTrigger as-child>
      <Button variant="outline" class="justify-start p-6 text-left font-normal">
        <span v-if="props.modelValue">
          {{ format(props.modelValue, 'dd.MM.yyyy') }}
        </span>
        <span v-else class="text-muted-foreground">
          {{ placeholder ?? 'Выберите дату' }}
        </span>
      <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-auto p-0">
      <Calendar
        v-model="internalDate"
        @update:model-value="handleSelect"
      />
    </PopoverContent>
  </Popover>
</template>