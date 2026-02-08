<script lang="ts" setup>
import { Head, Link } from '@inertiajs/vue3';

type Blog = {
    id: number;
    title: string;
    date: string;
    content: string;
    featured_img_path: string;
};

const { blog } = defineProps<{
    blog: Blog;
}>();

const fmtDate = (str: string): string => {
    return new Date(str).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>
    <Head :title="blog.title" />

    <div class="mx-auto max-w-3xl space-y-6">
        <Link :href="route('blog.index')" class="text-sm text-muted-foreground hover:underline">
            &larr; Назад к блогу
        </Link>

        <div class="aspect-video overflow-hidden rounded-xl">
            <img
                :src="blog.featured_img_path"
                :alt="blog.title"
                class="h-full w-full object-cover"
            />
        </div>

        <div class="space-y-2">
            <p class="text-sm text-muted-foreground">{{ fmtDate(blog.date) }}</p>
            <h1 class="text-3xl font-bold">{{ blog.title }}</h1>
        </div>

        <article class="prose prose-sm max-w-none" v-html="blog.content"></article>
    </div>
</template>