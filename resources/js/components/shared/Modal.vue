<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/shadecn/dialog'

const props = defineProps<{
  open?: boolean
  title?: string
}>()

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void
}>()
</script>

<template>
  <Dialog :open="props.open" @update:open="emit('update:open', $event)">
    <DialogTrigger as-child>
      <slot name="trigger" />
    </DialogTrigger>

    <DialogContent>
      <DialogHeader v-if="props.title">
        <DialogTitle class="text-center leading-relaxed max-md:px-5">
          {{ props.title }}
        </DialogTitle>
      </DialogHeader>

      <slot />
    </DialogContent>
  </Dialog>
</template>