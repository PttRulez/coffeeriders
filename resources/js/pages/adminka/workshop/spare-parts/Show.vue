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
import { Plus, SquarePen, Trash2 } from 'lucide-vue-next';

type WorkshopSparePart = {
    id: number;
    name: string;
    comment: string | null;
    quantity: number;
    purchase_price_rub: number;
    sale_price_rub: number;
    photo_url: string | null;
    category: { id: number; name: string } | null;
    purchases: Array<{
        id: number;
        quantity: number;
        purchase_price_rub: number;
        purchased_at: string;
        comment: string | null;
        user: { id: number; name: string } | null;
    }>;
};

const { item } = defineProps<{ item: WorkshopSparePart }>();

const formatPrice = (price: number): string => `${new Intl.NumberFormat('ru-RU').format(price)} ₽`;
const formatDate = (value: string): string => new Date(value).toLocaleDateString('ru-RU');

const deleteItem = () => {
    if (!confirm(`Удалить запчасть "${item.name}"?`)) {
        return;
    }

    router.delete(
        route('adminka.workshop.spare-parts.destroy', {
            workshopSparePart: item.id,
        }),
    );
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-semibold">{{ item.name }}</h1>
            <div class="flex flex-wrap gap-2">
                <Button as-child>
                    <Link
                        :href="
                            route('adminka.workshop.spare-parts.purchases.create', {
                                workshopSparePart: item.id,
                            })
                        "
                    >
                        <Plus class="h-4 w-4" />
                    </Link>
                </Button>
                <Button as-child variant="outline">
                    <Link
                        :href="
                            route('adminka.workshop.spare-parts.edit', {
                                workshopSparePart: item.id,
                            })
                        "
                    >
                        <SquarePen class="h-4 w-4" />
                    </Link>
                </Button>
                <Button variant="destructive" @click="deleteItem">
                    <Trash2 class="h-4 w-4" />
                </Button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
            <div class="space-y-3">
                <img
                    v-if="item.photo_url"
                    :src="item.photo_url"
                    :alt="item.name"
                    class="h-72 w-full rounded-lg border object-cover"
                />
                <div
                    v-else
                    class="flex h-72 w-full items-center justify-center rounded-lg border bg-muted text-muted-foreground"
                >
                    Без фото
                </div>
            </div>

            <div class="space-y-2 rounded-lg border p-4">
                <p><span class="text-muted-foreground">Категория:</span> {{ item.category?.name ?? '—' }}</p>
                <p><span class="text-muted-foreground">Остаток:</span> {{ item.quantity }} шт.</p>
                <p>
                    <span class="text-muted-foreground">Средняя цена закупки:</span>
                    {{ formatPrice(item.purchase_price_rub) }}
                </p>
                <p><span class="text-muted-foreground">Цена продажи:</span> {{ formatPrice(item.sale_price_rub) }}</p>
                <div>
                    <p class="text-muted-foreground">Комментарий:</p>
                    <p v-if="item.comment" class="whitespace-pre-line break-words">{{ item.comment }}</p>
                    <p v-else class="text-muted-foreground">—</p>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <h2 class="text-lg font-semibold">Закупки</h2>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Дата</TableHead>
                        <TableHead>Количество</TableHead>
                        <TableHead>Цена закупки</TableHead>
                        <TableHead>Кто внёс</TableHead>
                        <TableHead>Комментарий</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="purchase in item.purchases" :key="purchase.id">
                        <TableCell>{{ formatDate(purchase.purchased_at) }}</TableCell>
                        <TableCell>{{ purchase.quantity }}</TableCell>
                        <TableCell>{{ formatPrice(purchase.purchase_price_rub) }}</TableCell>
                        <TableCell>{{ purchase.user?.name ?? '—' }}</TableCell>
                        <TableCell>
                            <span v-if="purchase.comment" class="whitespace-pre-line break-words">
                                {{ purchase.comment }}
                            </span>
                            <span v-else class="text-muted-foreground">—</span>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="!item.purchases.length">
                        <TableCell colspan="5" class="py-8 text-center text-muted-foreground">
                            Закупок пока нет
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
