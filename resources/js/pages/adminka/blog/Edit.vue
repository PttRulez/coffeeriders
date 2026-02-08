<script lang="ts" setup="">
import BlogForm from '@/pages/adminka/blog/BlogForm.vue';

defineProps<{
    blog: {
        id: number;
        content: string;
        date: string;
        featured_img_path: string;
        title: string;
    };
}>();
</script>

<template>
    <BlogForm :blog="blog" />
</template>