<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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
import { computed, ref } from 'vue';

type WorkshopService = {
    id: number;
    category: { id: number; name: string } | null;
    name: string;
    price_rub: number;
};

const { items } = defineProps<{ items: WorkshopService[] }>();
const selectedCategoryId = ref<string>('all');

const categoryOptions = computed(() =>
    Array.from(
        new Map(
            items
                .filter((item) => item.category)
                .map((item) => [item.category!.id, item.category!.name]),
        ).entries(),
    ).map(([id, name]) => ({ id, name })),
);

const filteredItems = computed(() => {
    if (selectedCategoryId.value === 'all') {
        return items;
    }

    return items.filter((item) => item.category?.id === Number(selectedCategoryId.value));
});

const groupedItems = computed(() => {
    const map = new Map<string, WorkshopService[]>();

    filteredItems.value.forEach((item) => {
        const categoryName = item.category?.name ?? 'Без категории';
        if (!map.has(categoryName)) {
            map.set(categoryName, []);
        }
        map.get(categoryName)!.push(item);
    });

    return Array.from(map.entries()).map(([category, services]) => ({
        category,
        services,
    }));
});

const deleteItem = (item: WorkshopService) => {
    if (!confirm(`Удалить услугу "${item.name}"?`)) {
        return;
    }

    router.delete(route('adminka.workshop-services.destroy', item.id));
};

const formatPrice = (price: number): string => `${new Intl.NumberFormat('ru-RU').format(price)} ₽`;
</script>

<template>
    <div class="mx-auto max-w-[1080px] space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <Button as-child>
                <Link :href="route('adminka.workshop-services.create')" class="w-fit">
                    Добавить услугу
                </Link>
            </Button>

            <div class="grid gap-1">
                <label for="services-category-filter" class="text-sm text-muted-foreground">
                    Фильтр по категории
                </label>
                <Select v-model="selectedCategoryId">
                    <SelectTrigger
                        id="services-category-filter"
                        class="h-10 min-w-56 bg-background px-3 text-sm"
                    >
                        <SelectValue placeholder="Все категории" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem value="all">Все категории</SelectItem>
                            <SelectItem
                                v-for="category in categoryOptions"
                                :key="category.id"
                                :value="String(category.id)"
                            >
                                {{ category.name }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Название</TableHead>
                    <TableHead>Цена</TableHead>
                    <TableHead class="text-right"></TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-for="group in groupedItems" :key="group.category">
                    <TableRow>
                        <TableCell colspan="3" class="bg-muted text-[16px] font-semibold">
                            {{ group.category }}
                        </TableCell>
                    </TableRow>

                    <TableRow v-for="item in group.services" :key="item.id">
                        <TableCell>{{ item.name }}</TableCell>
                        <TableCell>{{ formatPrice(item.price_rub) }}</TableCell>
                        <TableCell class="text-right">
                            <div class="flex justify-end gap-3">
                                <Link :href="route('adminka.workshop-services.edit', item.id)">
                                    <SquarePen class="cursor-pointer" />
                                </Link>
                                <button
                                    @click="deleteItem(item)"
                                    class="cursor-pointer text-red-600"
                                >
                                    <Trash />
                                </button>
                            </div>
                        </TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell class="h-[40px]"></TableCell>
                    </TableRow>
                </template>
                <TableRow v-if="!filteredItems.length">
                    <TableCell colspan="3" class="py-8 text-center text-muted-foreground">
                        По выбранной категории услуг нет
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
