<script setup lang="ts">
import { Button } from '@/components/ui/button';
import DesktopBoard from '@/pages/adminka/workshop/repair-orders/DesktopBoard.vue';
import MobileTabs from '@/pages/adminka/workshop/repair-orders/MobileTabs.vue';
import { RepairOrder, RepairOrderStatus } from '@/pages/adminka/workshop/repair-orders/types';
import { Role } from '@/types/enums';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { itemsByStatus } = defineProps<{
    itemsByStatus: Record<RepairOrderStatus, RepairOrder[]>;
}>();
const page = usePage();
const isAdmin = computed(() => page.props.auth.user?.role === Role.Admin);

const statusLabel: Record<RepairOrderStatus, string> = {
    pending: 'Ожидание',
    in_work: 'В работе',
    finished: 'Завершено',
};

const statuses: RepairOrderStatus[] = ['pending', 'in_work', 'finished'];
const totalItems = computed(() =>
    statuses.reduce((total, status) => total + (itemsByStatus[status]?.length ?? 0), 0),
);

</script>

<template>
    <div class="mx-auto max-w-[1200px] space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <Button as-child>
                    <Link :href="route('adminka.workshop.repair-orders.create')">+</Link>
                </Button>
                <Button v-if="isAdmin" as-child variant="outline">
                    <Link :href="route('adminka.workshop.repair-orders.report')">Отчеты</Link>
                </Button>
            </div>
        </div>

        <div v-if="!totalItems" class="rounded-lg border p-8 text-center text-muted-foreground">
            Велосипедов в ремонте пока нет
        </div>

        <div v-else>
            <div class="lg:hidden">
                <MobileTabs
                    :items-by-status="itemsByStatus"
                    :statuses="statuses"
                    :status-label="statusLabel"
                    :is-admin="isAdmin"
                />
            </div>
            <div class="hidden lg:block">
                <DesktopBoard
                    :items-by-status="itemsByStatus"
                    :statuses="statuses"
                    :status-label="statusLabel"
                    :is-admin="isAdmin"
                />
            </div>
        </div>
    </div>
</template>
