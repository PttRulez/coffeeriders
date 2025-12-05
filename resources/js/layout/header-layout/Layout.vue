<script setup lang="ts">
import AppContent from '@/layout/components/AppContent.vue';
import Footer from '@/layout/header-layout/Footer.vue';
import Header from '@/layout/header-layout/Header.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <Header :breadcrumbs="breadcrumbs" />
    <AppContent>
        <slot />
    </AppContent>
    <Footer />
</template>
