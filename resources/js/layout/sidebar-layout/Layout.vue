<script setup lang="ts">
import AppContent from '@/layout/components/AppContent.vue';
import Sidebar from '@/layout/sidebar-layout/Sidebar.vue';
import Header from '@/layout/sidebar-layout/Header.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { SidebarProvider } from '@/components/ui/sidebar';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const isOpen = usePage().props.sidebarOpen;

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <SidebarProvider :default-open="isOpen">
        <Sidebar />
        <AppContent variant="sidebar">
            <Header :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </SidebarProvider>
</template>
