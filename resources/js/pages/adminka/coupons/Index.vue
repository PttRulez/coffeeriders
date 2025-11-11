<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

// shadcn-ui
import { Badge } from '@/components/shadecn/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/shadecn/table';

// lucide icons
import axios from 'axios';
import { Check, Pencil, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    items: Array<{
        id: number;
        code: string;
        description: string | null;
        discount_type: 'percent' | 'fixed';
        discount_value: number;
        service_type: 'cycling' | 'bike_rent';
        is_active: boolean;
        starts_at: string | null;
        ends_at: string | null;
        total_usages?: number;
        created_at: string;
        updated_at: string;
    }>;
}>();
const toggling = ref<number | null>(null);
const toggleActive = async (couponId: number) => {
    toggling.value = couponId;
    try {
        const { data } = await axios.patch(route('adminka.coupons.toggle-active', couponId));
        const coupon = props.items.find((i) => i.id === couponId);
        if (coupon) coupon.is_active = data.new_state;
        toast.success(`Купон ${data.new_state ? 'активирован' : 'деактивирован'}`);
    } catch (e) {
        toast.error('Ошибка при смене статуса');
    } finally {
        toggling.value = null;
    }
};

console.log(props.items);

const fmtDate = (str: string | null): string => {
    if (!str) return '';
    const d = new Date(str);
    return d.toLocaleDateString('ru-RU');
};

const discountLabel = (t: 'percent' | 'fixed', v: number) => (t === 'percent' ? `${v}%` : `${v} ₽`);

const serviceLabel = (s: 'cycling' | 'bike_rent') =>
    s === 'cycling' ? 'Сайклинг' : 'Аренда велосипеда';
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Купоны</h1>
            <Link
                :href="route('adminka.coupons.create')"
                class="rounded-lg bg-green-600 px-4 py-2 text-white"
            >
                + Создать
            </Link>
        </div>

        <div class="overflow-hidden rounded-xl border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Код</TableHead>
                        <TableHead>Тип / Значение</TableHead>
                        <TableHead>Услуга</TableHead>
                        <TableHead>Период</TableHead>
                        <TableHead class="text-center">Активен</TableHead>
                        <TableHead class="text-center">Использований</TableHead>
                        <TableHead class="w-24 text-right">Действия</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="it in props.items" :key="it.id">
                        <TableCell class="font-mono text-sm">{{ it.code }}</TableCell>

                        <TableCell>
                            <div class="flex items-center gap-2">
                                <Badge variant="secondary" class="uppercase">{{
                                    it.discount_type
                                }}</Badge>
                                <span class="font-medium">
                                    {{ discountLabel(it.discount_type, it.discount_value) }}
                                </span>
                            </div>
                        </TableCell>

                        <TableCell>{{ serviceLabel(it.service_type) }}</TableCell>

                        <TableCell class="text-sm">
                            <div>с: {{ fmtDate(it.starts_at) }}</div>
                            <div>по: {{ fmtDate(it.ends_at) }}</div>
                        </TableCell>

                        <TableCell class="text-center">
                            <button
                                class="inline-flex cursor-pointer items-center justify-center"
                                :disabled="toggling === it.id"
                                @click="toggleActive(it.id)"
                            >
                                <Check
                                    v-if="it.is_active"
                                    class="inline-block font-bold text-green-500"
                                    :stroke-width="3"
                                />
                                <X v-else class="inline-block text-red-500" :stroke-width="3" />
                            </button>
                        </TableCell>

                        <TableCell class="text-center">
                            {{ it.total_usages ?? 0 }}
                        </TableCell>

                        <TableCell class="text-right">
                            <div class="flex justify-end gap-2">
                                <Link
                                    :href="route('adminka.coupons.edit', it.id)"
                                    class="inline-flex items-center gap-1 rounded-md border px-2 py-1 text-xs"
                                >
                                    <Pencil class="h-4 w-4" /> Ред.
                                </Link>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="!props.items.length">
                        <TableCell
                            colspan="8"
                            class="py-10 text-center text-sm text-muted-foreground"
                        >
                            Купоны не найдены
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
