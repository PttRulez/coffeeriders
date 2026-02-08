<script lang="ts" setup>
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import axios from 'axios';
import { Check, Pencil, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

type Blog = {
    id: number;
    title: string;
    date: string;
    is_published: boolean;
};

const props = defineProps<{
    blogs: Blog[];
}>();

const toggling = ref<number | null>(null);

const togglePublished = async (blog: Blog) => {
    toggling.value = blog.id;
    try {
        const { data } = await axios.patch(route('adminka.blog.toggle-published', blog.id));
        blog.is_published = data.new_state;
        toast.success(`Статья ${data.new_state ? 'опубликована' : 'снята с публикации'}`);
    } catch {
        toast.error('Ошибка при смене статуса');
    } finally {
        toggling.value = null;
    }
};

const fmtDate = (str: string): string => {
    return new Date(str).toLocaleDateString('ru-RU');
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Блог</h1>
            <Button as-child>
                <Link :href="route('adminka.blog.create')">+ Создать</Link>
            </Button>
        </div>

        <div class="overflow-hidden rounded-xl border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Дата</TableHead>
                        <TableHead>Название</TableHead>
                        <TableHead class="text-center">Опубликовано</TableHead>
                        <TableHead class="w-24 text-right">Действия</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="blog in blogs" :key="blog.id">
                        <TableCell class="text-sm">{{ fmtDate(blog.date) }}</TableCell>

                        <TableCell class="font-medium">{{ blog.title }}</TableCell>

                        <TableCell class="text-center">
                            <button
                                class="inline-flex cursor-pointer items-center justify-center"
                                :disabled="toggling === blog.id"
                                @click="togglePublished(blog)"
                            >
                                <Check
                                    v-if="blog.is_published"
                                    class="text-green-500"
                                    :stroke-width="3"
                                />
                                <X v-else class="text-red-500" :stroke-width="3" />
                            </button>
                        </TableCell>

                        <TableCell class="text-right">
                            <Link
                                :href="route('adminka.blog.edit', blog.id)"
                                class="inline-flex items-center gap-1 rounded-md border px-2 py-1 text-xs"
                            >
                                <Pencil class="h-4 w-4" /> Ред.
                            </Link>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="!blogs.length">
                        <TableCell
                            colspan="4"
                            class="py-10 text-center text-sm text-muted-foreground"
                        >
                            Записей пока нет
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>