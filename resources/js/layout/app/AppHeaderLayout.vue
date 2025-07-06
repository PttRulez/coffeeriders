<script setup lang="ts">
import AppContent from '@/layout/components/AppContent.vue';
import AppHeader from '@/layout/components/AppHeader.vue';
import AppShell from '@/layout/components/AppShell.vue';
import type { BreadcrumbItemType } from '@/types';

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
            <slot />
        </AppContent>
    </AppShell>
</template>
