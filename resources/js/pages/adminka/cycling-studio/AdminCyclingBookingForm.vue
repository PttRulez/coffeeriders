<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';
import DatePicker from '@/components/shared/DatePicker.vue';
import HourPicker from '@/components/shared/HourPicker.vue';
import { Button } from '@/components/ui/button';
import { useTypedForm } from '@/composables/useTypedForm';
import { dateValueToIso } from '@/helpers';
import { AppPageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import type { DateValue } from '@internationalized/date';
import { today } from '@internationalized/date';
import axios from 'axios';
import { type Ref, ref, watch } from 'vue';
import FormCheckBox from '@/components/form-elements/FormCheckBox.vue';

const bikes = ref<
    | {
          id: number;
          is_zwift_bike: boolean;
          name: string;
      }[]
    | null
>(null);

type BookingForm = {
    cycling_station_ids: number[];
    starts_at: string | null;
    ends_at: string | null;
    user_id: number;
};

const page = usePage<AppPageProps>();
const { user } = page.props.auth;

const form = useTypedForm<BookingForm>({
    cycling_station_ids: [],
    starts_at: null,
    ends_at: null,
    user_id: user.id,
});

// Даты
const selectedDate = ref<DateValue | null>(
    today('Europe/Moscow') as DateValue,
) as Ref<DateValue | null>;
const selectedStartTime = ref<string>('');
const selectedEndTime = ref<string>('');

watch([selectedDate, selectedStartTime, selectedEndTime], async ([date, startTime, endTime]) => {
    if (date && startTime) {
        const isoDate = dateValueToIso(date as DateValue);
        const startDatetime = `${isoDate}T${startTime}:00`;
        let endDatetime = null;
        if (endTime) {
            endDatetime = `${isoDate}T${endTime}:00`;
        }
        form.starts_at = startDatetime;
        form.ends_at = endDatetime;

        try {
            const { data } = await axios.post(route('adminka.cycling-studio.bike-check'), {
                startDatetime,
                endDatetime,
            });
            bikes.value = data.stations;
        } catch (e) {
            console.error('Ошибка при запросе:', e);
        }
    }
});
const onBikeSelect = (checked: boolean, bikeId: number) => {
    if (checked) {
        form.cycling_station_ids = [...form.cycling_station_ids, bikeId];
    } else {
        form.cycling_station_ids = form.cycling_station_ids.filter((id) => id !== bikeId);
    }
};

</script>

<template>
    <form
        @submit.prevent="() => form.post(route('adminka.cycling-studio.store'))"
        class="mx-auto flex w-fit flex-col gap-5 rounded-4xl border border-sidebar-border/80 p-10"
    >
        <h1>Бронь занятия в студии</h1>

        <section class="flex gap-5 max-md:flex-col">
            <DatePicker v-model="selectedDate" placeholder="Дата занятия" />

            <HourPicker
                v-if="selectedDate"
                v-model="selectedStartTime"
                :minHour="7"
                :maxHour="20"
                class="w-full! bg-red-300"
                placeholer="Начало"
            />

            <HourPicker
                v-if="selectedDate && selectedStartTime"
                v-model="selectedEndTime"
                :minHour="parseInt(selectedStartTime) + 1"
                :maxHour="22"
                class="w-full! bg-red-300"
                placeholer="Конец"
            />
            <div v-else></div>
        </section>
        <InputError class="text-xs!" :message="form.errors.starts_at" />

        <template v-for="bike in bikes" :key="bike.id">
            <FormCheckBox
                :label="`${bike.name} ${bike.is_zwift_bike ? 'zwift байк' : 'шоссер'}`"
                @change="(checked) => onBikeSelect(checked, bike.id)"
            />
        </template>

        <Button type="submit" class="mx-auto w-fit"> Забронировать</Button>
    </form>
</template>
