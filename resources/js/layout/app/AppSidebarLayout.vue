<script setup lang="ts">
import AppContent from '@/layout/components/AppContent.vue';
import AppShell from '@/layout/components/AppShell.vue';
import AppSidebar from '@/layout/components/AppSidebar.vue';
import AppSidebarHeader from '@/layout/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
