<script setup lang="ts">
import { SidebarInset } from '@/components/shadecn/sidebar';
import { computed } from 'vue';

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
}

const props = defineProps<Props>();
const className = computed(() => props.class);
</script>

<template>
    <SidebarInset v-if="props.variant === 'sidebar'" :class="className">
        <slot />
    </SidebarInset>
    <main
        v-else
        class="my-container relative flex-1 mx-auto mt-5 md:mt-10 px-2 md:px-10 pt-16 pb-16 md:w-full min-h-full! md:max-w-7xlgap-4 rounded-xl"
        :class="className"
    >
        <slot />
    </main>
</template>
