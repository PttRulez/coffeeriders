<script setup lang="ts">
import Layout from '@/layout/header-layout/Layout.vue';
import type { BreadcrumbItemType } from '@/types';
import FlashToaster from '@/components/shared/FlashToaster.vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <Layout :breadcrumbs="breadcrumbs">
        <FlashToaster />
        <slot />
    </Layout>
</template>
