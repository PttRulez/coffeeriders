<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { CameraOff, SquarePen, Trash2 } from 'lucide-vue-next';
import { RepairOrder, RepairOrderStatus } from './types';

const props = defineProps<{
    itemsByStatus: Record<RepairOrderStatus, RepairOrder[]>;
    statuses: RepairOrderStatus[];
    statusLabel: Record<RepairOrderStatus, string>;
    isAdmin: boolean;
}>();

const deleteItem = (item: RepairOrder) => {
    if (!confirm(`Удалить заказ ремонта "${item.bike_name}"?`)) {
        return;
    }

    router.delete(route('adminka.workshop.repair-orders.destroy', item.id));
};

const formatDate = (value: string): string => new Date(value).toLocaleDateString('ru-RU');
const phoneHref = (phone: string): string => `tel:${phone.replace(/[^\d+]/g, '')}`;
const telegramUsername = (value: string): string =>
    value.trim().replace(/^https?:\/\/t\.me\//i, '').replace(/^t\.me\//i, '').replace(/^@/, '');
const telegramHref = (value: string): string => `https://t.me/${telegramUsername(value)}`;
</script>

<template>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <section v-for="status in props.statuses" :key="`column-${status}`" class="rounded-xl border bg-card">
            <div class="border-b bg-card px-4 py-3">
                <h2 class="text-sm font-semibold">
                    {{ props.statusLabel[status] }} ({{ props.itemsByStatus[status].length }})
                </h2>
            </div>

            <div class="max-h-[70vh] space-y-3 overflow-y-auto p-3">
                <div
                    v-if="!props.itemsByStatus[status].length"
                    class="rounded-lg border p-6 text-center text-sm text-muted-foreground"
                >
                    Колонка пока пустая
                </div>

                <div
                    v-for="item in props.itemsByStatus[status]"
                    :key="item.id"
                    class="overflow-hidden rounded-2xl border bg-card shadow-sm"
                >
                    <img
                        v-if="item.photos[0]?.photo_url"
                        :src="item.photos[0].photo_url"
                        :alt="item.bike_name"
                        class="aspect-square w-full object-cover"
                    />
                    <div
                        v-else
                        class="flex aspect-square w-full items-center justify-center bg-muted text-muted-foreground"
                    >
                        <CameraOff class="h-8 w-8" />
                    </div>

                    <div class="space-y-2 p-3">
                        <div class="flex items-center gap-2">
                            <p class="text-base font-semibold">{{ item.bike_name }}</p>
                            <p v-if="item.mechanic?.name" class="text-muted-foreground">
                                ({{ item.mechanic?.name }})
                            </p>
                        </div>

                        <div class="space-y-1 text-sm">
                            <p v-if="item.client_phone">
                                <a
                                    :href="phoneHref(item.client_phone)"
                                    class="font-medium text-blue-600 underline decoration-blue-600/70 underline-offset-2 transition-colors hover:text-blue-700 focus-visible:ring-2 focus-visible:ring-blue-500/40 focus-visible:outline-none"
                                >
                                    {{ item.client_phone }}
                                </a>
                            </p>
                            <p v-if="item.client_telegram">
                                <a
                                    :href="telegramHref(item.client_telegram)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="font-medium text-blue-600 underline decoration-blue-600/70 underline-offset-2 transition-colors hover:text-blue-700 focus-visible:ring-2 focus-visible:ring-blue-500/40 focus-visible:outline-none"
                                >
                                    @{{ telegramUsername(item.client_telegram) }}
                                </a>
                            </p>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-1">
                            <p class="text-xs text-muted-foreground">{{ formatDate(item.created_at) }}</p>
                            <button
                                v-if="props.isAdmin"
                                type="button"
                                class="cursor-pointer text-red-600"
                                @click="deleteItem(item)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                            <Link :href="route('adminka.workshop.repair-orders.edit', item.id)">
                                <SquarePen class="h-4 w-4 cursor-pointer" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
