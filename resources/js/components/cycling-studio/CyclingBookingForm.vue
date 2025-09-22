<script setup lang="ts">
import DatePicker from '@/components/DatePicker.vue';
import HourPicker from '@/components/HourPicker.vue';
import { Button } from '@/components/shadecn/button';
import { Label } from '@/components/shadecn/label';
import { RadioGroup, RadioGroupItem } from '@/components/shadecn/radio-group';
import { useTypedForm } from '@/composables/useTypedForm';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, watch } from 'vue';
import type { DateValue } from '@internationalized/date';
import { today } from '@internationalized/date';
import { dateValueToIso } from '@/helpers';

const bikes = ref<
    | {
          id: number;
          is_zwift_bike: boolean;
          name: string;
      }[]
    | null
>(null);

type BookingForm = {
    cycling_station_id: number | null;
    starts_at: string | null;
    user_id: number;
};
const page = usePage();
const { user } = page.props.auth;

const form = useTypedForm<BookingForm>({
    cycling_station_id: null,
    user_id: user.id,
    starts_at: null,
});

const selectedDate = ref<DateValue | null>(today("Europe/Moscow"));
const selectedTime = ref<string>('');

watch([selectedDate, selectedTime], async ([date, time]) => {
  if (date && time) {
    const isoDate = dateValueToIso(date as DateValue);
    const datetime = `${isoDate}T${time}:00`;
    form.starts_at = datetime;

    try {
      const { data } = await axios.post(route('cycling-studio.bike-check'), {
        datetime,
      });
      bikes.value = data.stations;
    } catch (e) {
      console.error('Ошибка при запросе:', e);
    }
  }
});

const submit = () => {
    form.post(route('cycling-studio.booking.store'));
};
</script>

<template>
    <form
        @submit.prevent="submit"
        class="mx-auto flex w-fit flex-col gap-5 rounded-4xl border border-sidebar-border/80 p-10"
    >
        <h1>Бронь занятия в студии</h1>

        <section class="flex gap-5 max-md:flex-col">
            <DatePicker v-model="selectedDate" placeholder="Дата занятия" />

            <HourPicker v-if="selectedDate" v-model="selectedTime" :minHour="7" :maxHour="20" />
        </section>

        <RadioGroup
            v-if="bikes?.length"
            v-model="form.cycling_station_id"
            :orientation="'vertical'"
        >
            <div v-for="bike in bikes" class="flex items-center space-x-2">
                <RadioGroupItem :id="`bike_${bike.id}`" :value="bike.id" />
                <Label :for="`bike_${bike.id}`"
                    >{{ bike.name }} ({{ bike.is_zwift_bike ? 'zwift байк' : 'шоссер' }})</Label
                >
            </div>
        </RadioGroup>

        <Button type="submit" class="mx-auto w-fit"> Забронировать</Button>
    </form>
</template>
