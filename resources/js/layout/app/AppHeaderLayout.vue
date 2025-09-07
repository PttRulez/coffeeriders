<script setup lang="ts">
import AppContent from '@/layout/components/AppContent.vue';
import AppHeader from '@/layout/components/AppHeader.vue';
import AppShell from '@/layout/components/AppShell.vue';
import type { BreadcrumbItemType } from '@/types';
import { Toaster } from 'vue-sonner'

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell class="flex-col">
        <AppHeader :breadcrumbs="breadcrumbs"/>
        <AppContent>
            <Toaster richColors position="top-center" />
            <slot />
        </AppContent>
    </AppShell>
</template>
