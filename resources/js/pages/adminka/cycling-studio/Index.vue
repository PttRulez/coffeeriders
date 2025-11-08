<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import { Button } from '@/components/shadecn/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/shadecn/table';
import DatePicker from '@/components/shared/DatePicker.vue';
import Modal from '@/components/shared/Modal.vue';
import { useQuery } from '@/composables/useQuery';
import { dateTimeToTime, dateValueToIso } from '@/helpers';
import { CyclingActivity } from '@/types';
import { router } from '@inertiajs/vue3';
import type { DateValue } from '@internationalized/date';
import { parseDate, today } from '@internationalized/date';
import { Check, Trash, X } from 'lucide-vue-next';
import { ref } from 'vue';

type Props = {
    activities: CyclingActivity[];
};
const props = defineProps<Props>();
const openDistanceModals = ref<Record<number, boolean>>(
    Object.fromEntries(props.activities.map((a) => [a.id, false])),
);
const distances = ref<Record<number, number>>(
    Object.fromEntries(props.activities.map((a) => [a.id, a.distance])),
);

const query = useQuery();
const queryDate = query.get('date');

const date = ref<DateValue>(
    (() => {
        try {
            return queryDate ? parseDate(queryDate) : today('Europe/Moscow');
        } catch {
            return today('Europe/Moscow');
        }
    })(),
);

const reloadActivities = (val: DateValue | null) => {
    if (!val) return;
    router.get(
        route('adminka.cycling-studio.index'),
        { date: dateValueToIso(val) },
        { preserveState: true, preserveScroll: true },
    );
};

const deleteActivity = (id: number) => {
    router.delete(route('adminka.cycling-studio.activities.destroy', id), {
        onSuccess: () => {
            router.reload({ only: ['activities'] });
        },
    });
};

const submit = (id: number) => {
    router.put(
        route('adminka.cycling-studio.activities.update', id),
        {
            distance: distances.value[id],
        },
        {
            onSuccess: () => {
                openDistanceModals.value[id] = false;
                router.reload({ only: ['activities'] });
            },
        },
    );
};

console.log(props.activities);
</script>

<template>
    <DatePicker v-model="date" @update:model-value="reloadActivities" />
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead>время</TableHead>
                <TableHead>оплачена</TableHead>
                <TableHead>станок</TableHead>
                <TableHead>имя</TableHead>
                <TableHead>телефон</TableHead>
                <TableHead>телеграм</TableHead>
                <TableHead>купон</TableHead>
                <TableHead>скидка</TableHead>
                <TableHead>цена</TableHead>
                <TableHead>дистанция</TableHead>
                <TableHead></TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="activity in props.activities" :key="activity.id">
                <TableCell>
                    {{ dateTimeToTime(activity.starts_at) }}
                </TableCell>

                <TableCell>
                    <Check
                        v-if="activity.is_paid"
                        class="inline-block font-bold text-green-500"
                        :stroke-width="3"
                    />
                    <X v-else class="inline-block text-red-500" :stroke-width="3" />
                </TableCell>

                <TableCell>
                    {{ activity.cycling_station.name }}
                    ({{ activity.cycling_station.is_zwift_bike ? 'Zwift' : 'Шоссер' }})
                </TableCell>

                <TableCell>
                    {{ activity.user.name }}
                </TableCell>

                <TableCell>
                    {{ activity.user.phone }}
                </TableCell>

                <TableCell>
                    <a
                        v-if="activity.user.telegram_username"
                        :href="`https://t.me/${activity.user.telegram_username}`"
                        class="flex items-center text-blue-400"
                        target="_blank"
                    >
                        {{ activity.user.telegram_username }}
                    </a>
                </TableCell>

                <TableCell>
                    {{ activity.coupon_usage?.coupon_code }}
                </TableCell>

                <TableCell>
                    {{ activity.coupon_usage?.applied_discount }}
                </TableCell>

                <TableCell>
                    {{ activity.coupon_usage?.final_price }}
                </TableCell>

                <TableCell class="cursor-pointer">
                    <Modal
                        :open="openDistanceModals[activity.id]"
                        @update:open="openDistanceModals[activity.id] = $event"
                        title=""
                    >
                        <template #trigger>
                            <span>{{ activity.distance }}</span>
                        </template>

                        <form @submit.prevent="submit(activity.id)">
                            <FormInput
                                v-model="distances[activity.id]"
                                field-name="distance"
                                type="number"
                                placeholder="Введите дистанцию"
                            />

                            <Button type="submit" class="mt-2 w-full"> Сохранить</Button>
                        </form>
                    </Modal>
                </TableCell>

                <TableCell>
                    <Trash @click="deleteActivity(activity.id)" class="cursor-pointer" />
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
