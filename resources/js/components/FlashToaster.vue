<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import { toast, Toaster } from 'vue-sonner'

const page = usePage()

watch(
  () => page.props.flash,
  (flash) => {
    if (!flash) return

    if (flash.success) {
      toast.success(flash.success)
    }

    if (flash.error) {
      toast.error(flash.error)
    }

    if (flash.warning) {
      toast.warning(flash.warning)
    }

    if (flash.info) {
      toast(flash.info)
    }
  },
  { deep: true }
)
</script>

<template>
  <Toaster richColors position="top-center" />
</template>