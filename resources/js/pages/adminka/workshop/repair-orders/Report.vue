<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import { Button } from '@/components/ui/button';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

type RepairOrdersFilters = {
    period_from: string | null;
    period_to: string | null;
};

type RepairOrdersSummary = {
    workshop_works_income_rub: number;
    workshop_spare_parts_income_rub: number;
    workshop_income_rub: number;
    mechanic_income_rub: number;
    mechanics: Array<{
        mechanic_id: number;
        mechanic_name: string;
        income_rub: number;
    }>;
};

const { filters, summary } = defineProps<{
    filters: RepairOrdersFilters;
    summary: RepairOrdersSummary;
}>();

const periodFrom = ref(filters.period_from ?? '');
const periodTo = ref(filters.period_to ?? '');

const formatPrice = (price: number): string => `${new Intl.NumberFormat('ru-RU').format(price)} ₽`;
const toDateInputValue = (date: Date): string => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const currentMonthPeriod = () => {
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

    return {
        period_from: toDateInputValue(firstDay),
        period_to: toDateInputValue(lastDay),
    };
};

const applyPeriodFilter = () => {
    router.get(
        route('adminka.workshop.repair-orders.report'),
        {
            period_from: periodFrom.value || undefined,
            period_to: periodTo.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const resetPeriodFilter = () => {
    const currentMonth = currentMonthPeriod();
    periodFrom.value = currentMonth.period_from;
    periodTo.value = currentMonth.period_to;
    applyPeriodFilter();
};
</script>

<template>
    <div class="mx-auto max-w-[1200px] space-y-5">
        <div class="flex items-center justify-between gap-3">
            <h1 class="text-xl font-semibold">Отчеты мастерской</h1>
            <Button as-child variant="outline">
                <Link :href="route('adminka.workshop.repair-orders.index')">К заказам</Link>
            </Button>
        </div>

        <section class="rounded-xl border p-4 space-y-4">
            <div class="grid gap-3 md:grid-cols-[1fr_1fr_auto_auto] md:items-end">
                <FormInput
                    field-name="period_from"
                    v-model="periodFrom"
                    type="date"
                    label="Период с"
                />
                <FormInput
                    field-name="period_to"
                    v-model="periodTo"
                    type="date"
                    label="Период по"
                />
                <Button type="button" @click="applyPeriodFilter">Применить</Button>
                <Button type="button" variant="outline" @click="resetPeriodFilter">Сбросить</Button>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 text-sm">
                <div class="rounded-lg border p-3">
                    <p class="text-muted-foreground">Мастерская: работы</p>
                    <p class="text-base font-semibold">
                        {{ formatPrice(summary.workshop_works_income_rub) }}
                    </p>
                </div>
                <div class="rounded-lg border p-3">
                    <p class="text-muted-foreground">Мастерская: запчасти</p>
                    <p class="text-base font-semibold">
                        {{ formatPrice(summary.workshop_spare_parts_income_rub) }}
                    </p>
                </div>
                <div class="rounded-lg border p-3">
                    <p class="text-muted-foreground">Мастерская: всего</p>
                    <p class="text-base font-semibold">{{ formatPrice(summary.workshop_income_rub) }}</p>
                </div>
                <div class="rounded-lg border p-3">
                    <p class="text-muted-foreground">Механики: всего</p>
                    <p class="text-base font-semibold">{{ formatPrice(summary.mechanic_income_rub) }}</p>
                </div>
            </div>

            <div v-if="summary.mechanics.length" class="space-y-1">
                <p class="text-sm text-muted-foreground">Заработок механиков за период</p>
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="row in summary.mechanics"
                        :key="row.mechanic_id"
                        class="rounded-lg border p-3 text-sm"
                    >
                        <p class="font-medium">{{ row.mechanic_name }}</p>
                        <p class="text-muted-foreground">{{ formatPrice(row.income_rub) }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
