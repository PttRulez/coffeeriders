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
import { Link } from '@inertiajs/vue3';
import { CameraOff } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type WorkshopSparePart = {
    id: number;
    category: { id: number; name: string } | null;
    name: string;
    comment: string | null;
    quantity: number;
    purchase_price_rub: number;
    sale_price_rub: number;
    photo_url: string | null;
};

const { items } = defineProps<{ items: WorkshopSparePart[] }>();
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
    const map = new Map<string, WorkshopSparePart[]>();

    filteredItems.value.forEach((item) => {
        const categoryName = item.category?.name ?? 'Без категории';
        if (!map.has(categoryName)) {
            map.set(categoryName, []);
        }
        map.get(categoryName)!.push(item);
    });

    return Array.from(map.entries()).map(([category, spareParts]) => ({
        category,
        spareParts,
    }));
});

const formatPrice = (price: number): string => `${new Intl.NumberFormat('ru-RU').format(price)} ₽`;
</script>

<template>
    <div class="mx-auto max-w-[1200px] space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div class="flex gap-2">
                <Button as-child>
                    <Link :href="route('adminka.workshop.spare-parts.create')" class="w-fit">
                        +
                    </Link>
                </Button>
                <Button as-child variant="outline">
                    <Link
                        :href="route('adminka.workshop.spare-part-purchases.index')"
                        class="w-fit"
                    >
                        Закупки
                    </Link>
                </Button>
                <Button as-child variant="outline">
                    <Link
                        :href="route('adminka.workshop.spare-part-categories.index')"
                        class="w-fit"
                    >
                        Категории
                    </Link>
                </Button>
            </div>

            <div class="grid gap-1">
                <label for="spare-parts-category-filter" class="text-sm text-muted-foreground">
                    Фильтр по категории
                </label>
                <Select v-model="selectedCategoryId">
                    <SelectTrigger
                        id="spare-parts-category-filter"
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

        <div
            v-if="!filteredItems.length"
            class="rounded-lg border p-8 text-center text-muted-foreground"
        >
            По выбранной категории запчастей нет
        </div>

        <section v-for="group in groupedItems" :key="group.category" class="space-y-3">
            <h2 class="text-lg font-semibold">{{ group.category }}</h2>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <div class="rounded-2xl shadow-2xl" v-for="item in group.spareParts" :key="item.id">
                    <Link
                        :href="
                            route('adminka.workshop.spare-parts.show', {
                                workshopSparePart: item.id,
                            })
                        "
                    >
                        <img
                            v-if="item.photo_url"
                            :src="item.photo_url"
                            :alt="item.name"
                            class="aspect-square w-full rounded-md object-cover"
                        />
                        <div
                            v-else
                            class="aspect-square w-full rounded-md bg-muted flex items-center justify-center text-muted-foreground"
                        >
                            <CameraOff class="h-8 w-8" />
                        </div>
                    </Link>
                    <div class="flex items-center gap-5 p-3">
                        <div class="">
                            <p class="text-xl font-bold">
                                {{ formatPrice(item.sale_price_rub) }}
                                <span class="text-sm text-muted-foreground"
                                    >( {{ item.quantity }} шт. )</span
                                >
                            </p>
                            <Link
                                :href="
                                    route('adminka.workshop.spare-parts.show', {
                                        workshopSparePart: item.id,
                                    })
                                "
                                class="hover:underline"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
