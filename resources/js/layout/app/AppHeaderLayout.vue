<script setup lang="ts">
import AppContent from '@/layout/components/AppContent.vue';
import AppFooter from '@/layout/components/AppFooter.vue';
import AppHeader from '@/layout/components/AppHeader.vue';
import type { BreadcrumbItemType } from '@/types';
import FlashToaster from '@/components/FlashToaster.vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppHeader :breadcrumbs="breadcrumbs" />
    <AppContent>
        <FlashToaster />
        <slot />
    </AppContent>
    <AppFooter />
</template>
