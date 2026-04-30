<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Link, router } from '@inertiajs/vue3';
import { SquarePen, Trash } from 'lucide-vue-next';

type WorkshopSparePartCategory = {
    id: number;
    name: string;
    spare_parts_count: number;
};

const { items } = defineProps<{ items: WorkshopSparePartCategory[] }>();

const deleteItem = (item: WorkshopSparePartCategory) => {
    if (!confirm(`Удалить категорию "${item.name}"?`)) {
        return;
    }

    router.delete(route('adminka.workshop.spare-part-categories.destroy', item.id));
};
</script>

<template>
    <div class="space-y-5">
        <Button as-child>
            <Link :href="route('adminka.workshop.spare-part-categories.create')" class="w-fit">
                Добавить категорию запчастей
            </Link>
        </Button>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Название</TableHead>
                    <TableHead>Запчастей</TableHead>
                    <TableHead class="text-right"></TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="item in items" :key="item.id">
                    <TableCell>{{ item.name }}</TableCell>
                    <TableCell>{{ item.spare_parts_count }}</TableCell>
                    <TableCell class="text-right">
                        <div class="flex justify-end gap-3">
                            <Link :href="route('adminka.workshop.spare-part-categories.edit', item.id)">
                                <SquarePen class="cursor-pointer" />
                            </Link>
                            <button @click="deleteItem(item)" class="cursor-pointer text-red-600">
                                <Trash />
                            </button>
                        </div>
                    </TableCell>
                </TableRow>
                <TableRow v-if="!items.length">
                    <TableCell colspan="3" class="py-8 text-center text-muted-foreground">
                        Категорий запчастей пока нет
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
