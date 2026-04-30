<script setup lang="ts">
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

type WorkshopSparePartPurchase = {
    id: number;
    quantity: number;
    purchase_price_rub: number;
    purchased_at: string;
    comment: string | null;
    spare_part: {
        id: number;
        name: string;
        category: { id: number; name: string } | null;
    };
    user: {
        id: number;
        name: string;
    } | null;
};

const { items } = defineProps<{ items: WorkshopSparePartPurchase[] }>();

const deleteItem = (item: WorkshopSparePartPurchase) => {
    if (!confirm(`Удалить закупку "${item.spare_part.name}"?`)) {
        return;
    }

    router.delete(
        route('adminka.workshop.spare-parts.purchases.destroy', {
            workshopSparePart: item.spare_part.id,
            workshopSparePartPurchase: item.id,
        }),
    );
};

const formatPrice = (price: number): string => `${new Intl.NumberFormat('ru-RU').format(price)} ₽`;
const formatDate = (value: string): string => new Date(value).toLocaleDateString('ru-RU');
</script>

<template>
    <div class="space-y-5">
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Дата</TableHead>
<!--                    <TableHead>Категория</TableHead>-->
                    <TableHead>Запчасть</TableHead>
                    <TableHead>Количество</TableHead>
                    <TableHead>Цена закупки</TableHead>
                    <TableHead>Внёс</TableHead>
<!--                    <TableHead>Комментарий</TableHead>-->
                    <TableHead class="text-right"></TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="item in items" :key="item.id">
                    <TableCell>{{ formatDate(item.purchased_at) }}</TableCell>
<!--                    <TableCell>{{ item.spare_part.category?.name ?? '—' }}</TableCell>-->
                    <TableCell>{{ item.spare_part.name }}</TableCell>
                    <TableCell>{{ item.quantity }}</TableCell>
                    <TableCell>{{ formatPrice(item.purchase_price_rub) }}</TableCell>
                    <TableCell>{{ item.user?.name ?? '—' }}</TableCell>
<!--                    <TableCell>-->
<!--                        <span v-if="item.comment" class="whitespace-pre-line break-words">-->
<!--                            {{ item.comment }}-->
<!--                        </span>-->
<!--                        <span v-else class="text-muted-foreground">—</span>-->
<!--                    </TableCell>-->
                    <TableCell class="text-right">
                        <div class="flex justify-end gap-3">
                            <Link
                                :href="
                                    route('adminka.workshop.spare-parts.purchases.edit', {
                                        workshopSparePart: item.spare_part.id,
                                        workshopSparePartPurchase: item.id,
                                    })
                                "
                            >
                                <SquarePen class="cursor-pointer" />
                            </Link>
                            <button @click="deleteItem(item)" class="cursor-pointer text-red-600">
                                <Trash />
                            </button>
                        </div>
                    </TableCell>
                </TableRow>

                <TableRow v-if="!items.length">
                    <TableCell colspan="8" class="py-8 text-center text-muted-foreground">
                        Закупок пока нет
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
