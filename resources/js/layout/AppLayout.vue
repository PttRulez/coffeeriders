<script setup lang="ts">
import AppHeaderLayout from '@/layout/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';
// import { Toaster } from 'vue-sonner'

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppHeaderLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppHeaderLayout>
</template>
