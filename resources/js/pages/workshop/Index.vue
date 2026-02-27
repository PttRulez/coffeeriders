<script setup lang="ts">
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
import { computed, ref } from 'vue';

type WorkshopService = {
    id: number;
    category: { id: number; name: string } | null;
    name: string;
    price_rub: number;
    additional_info: string | null;
};

const props = defineProps<{ services: WorkshopService[] }>();
const selectedCategory = ref<string>('all');

const categoryOptions = computed(() =>
    Array.from(new Set(props.services.map((service) => service.category?.name ?? 'Без категории'))),
);

const filteredServices = computed(() => {
    if (selectedCategory.value === 'all') {
        return props.services;
    }

    return props.services.filter(
        (service) => (service.category?.name ?? 'Без категории') === selectedCategory.value,
    );
});

const groupedServices = computed(() => {
    const map = new Map<string, WorkshopService[]>();

    filteredServices.value.forEach((service) => {
        const categoryName = service.category?.name ?? 'Без категории';
        if (!map.has(categoryName)) {
            map.set(categoryName, []);
        }
        map.get(categoryName)!.push(service);
    });

    return Array.from(map.entries()).map(([category, items]) => ({
        category,
        items,
    }));
});

const formatPrice = (price: number): string => `${new Intl.NumberFormat('ru-RU').format(price)} ₽`;
</script>

<template>
    <Head title="Веломастерская Coffeeriders" />

    <div class="space-y-8  max-w-[920px] mx-auto">
        <section id="pricelist" class="space-y-6">
            <div
                class="flex flex-wrap items-end justify-between gap-3 max-md:flex-col max-md:items-center"
            >
                <h2>Прайс-лист услуг</h2>
                <div class="grid gap-1">
                    <Select v-model="selectedCategory">
                        <SelectTrigger class="h-10 min-w-56 bg-background px-3 text-sm">
                            <SelectValue placeholder="Все категории" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="all">Все категории</SelectItem>
                                <SelectItem
                                    v-for="category in categoryOptions"
                                    :key="category"
                                    :value="category"
                                >
                                    {{ category }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <div
                v-if="!filteredServices.length"
                class="rounded-lg border p-6 text-muted-foreground"
            >
                По выбранной категории услуг нет.
            </div>

            <Table class="table-fixed">
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-4/5">Услуга</TableHead>
                        <TableHead class="w-1/5 text-right">Цена от</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-for="group in groupedServices" :key="group.category">
                        <TableRow>
                            <TableCell colspan="2" class="bg-muted font-semibold">
                                {{ group.category }}
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="service in group.items" :key="service.id">
                            <TableCell class="w-4/5 break-words hyphens-auto whitespace-normal">
                                <span>{{ service.name }}</span>
                                <span
                                    v-if="service.additional_info"
                                    class="block text-sm break-words hyphens-auto whitespace-normal text-muted-foreground"
                                >
                                    {{ service.additional_info }}
                                </span>
                            </TableCell>
                            <TableCell class="w-1/5 text-right">
                                <div class="flex flex-col items-end">
                                    <span>{{ formatPrice(service.price_rub) }}</span>
                                </div>
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </section>
    </div>
</template>
