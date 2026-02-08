<script lang="ts" setup>
import { Head, Link } from '@inertiajs/vue3';

type Blog = {
    id: number;
    title: string;
    date: string;
    featured_img_path: string;
};

defineProps<{
    blogs: Blog[];
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
    <Head title="Блог" />

    <div class="space-y-8">
        <h1 class="text-2xl font-bold">Блог</h1>

        <div v-if="blogs.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="blog in blogs"
                :key="blog.id"
                :href="route('blog.show', blog.id)"
                class="group overflow-hidden rounded-xl border transition hover:shadow-lg"
            >
                <div class="aspect-video overflow-hidden">
                    <img
                        :src="blog.featured_img_path"
                        :alt="blog.title"
                        class="h-full w-full object-cover transition group-hover:scale-105"
                    />
                </div>
                <div class="space-y-2 p-4">
                    <p class="text-sm text-muted-foreground">{{ fmtDate(blog.date) }}</p>
                    <h2 class="text-lg font-semibold group-hover:underline">{{ blog.title }}</h2>
                </div>
            </Link>
        </div>

        <p v-else class="text-muted-foreground">Записей пока нет</p>
    </div>
</template>