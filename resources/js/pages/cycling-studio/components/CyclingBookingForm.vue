<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';
import { Button } from '@/components/shadecn/button';
import { Label } from '@/components/shadecn/label';
import { RadioGroup, RadioGroupItem } from '@/components/shadecn/radio-group';
import DatePicker from '@/components/shared/DatePicker.vue';
import HourPicker from '@/components/shared/HourPicker.vue';
import { useTypedForm } from '@/composables/useTypedForm';
import { dateValueToIso } from '@/helpers';
import { AppPageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import type { DateValue } from '@internationalized/date';
import { today } from '@internationalized/date';
import axios from 'axios';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const bikes = ref<{
          id: number;
          is_zwift_bike: boolean;
          name: string;
      }[]
    | null
>(null);

type BookingForm = {
    coupon_code?: string | null;
    cycling_station_id: number | null;
    pay: boolean;
    starts_at: string | null;
    user_id: number;
};

// ---- пропсы
const page = usePage<
    AppPageProps & {
        pricing: { service: 'cycling' | 'bike_rent'; base_price: number; final_price: number };
    }
>();
const { user } = page.props.auth;
const service = page.props.pricing.service;
const finalPrice = ref<number>(page.props.pricing.final_price);

// ---- купон (локальное состояние)
const couponCode = ref('');
const loadingCoupon = ref(false);

// ---- превью купона: сервер сам считает final_price
const applyCoupon = async () => {
    if (!couponCode.value) return;
    loadingCoupon.value = true;

    try {
        const { data } = await axios.post(route('pricing.preview'), {
            service,
            code: couponCode.value,
        });

        finalPrice.value = data.final_price;
        form.coupon_code = couponCode.value;

        form.clearErrors('coupon_code');

        toast.success(`Купон применён — скидка ${data.discount} руб.`);
    } catch (e: any) {
        console.log(e);
        const msg = e?.response?.data?.message ?? 'Купон недействителен';

        form.setError?.('coupon_code', msg);

        form.coupon_code = null;
        finalPrice.value = page.props.pricing.base_price;

        toast.error(msg);
    } finally {
        loadingCoupon.value = false;
    }
};

const form = useTypedForm<BookingForm>({
    cycling_station_id: null,
    coupon_code: null,
    pay: false,
    starts_at: null,
    user_id: user.id,
});

// Даты
const selectedDate = ref<DateValue | null>(today('Europe/Moscow'));
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

// Сабмиты
const submit = () => {
    form.post(route('cycling-studio.booking.store'));
};

const submitWithPayment = () => {
    form.pay = true;
    submit();
};

const submitWithoutPayment = () => {
    form.pay = false;
    submit();
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

            <HourPicker
                v-if="selectedDate"
                v-model="selectedTime"
                :minHour="7"
                :maxHour="20"
                class="w-full! bg-red-300"
            />
        </section>
        <InputError class="text-xs!" :message="form.errors.starts_at" />

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

        <Button
            type="button"
            class="mx-auto w-fit"
            v-if="user?.paid_cycling_count > 0"
            @click="submitWithoutPayment"
        >
            Забронировать и списать (осталось {{ user?.paid_cycling_count }})</Button
        >
        <template v-else>
            <section class="flex flex-col gap-2">
                <div class="flex items-end gap-3 max-md:flex-col" v-if="!user.is_coffeerider">
                    <div class="flex flex-1 flex-col gap-2 w-full">
                        <Label for="coupon">Купон</Label>
                        <input
                            id="coupon"
                            v-model="couponCode"
                            type="text"
                            class="h-10 rounded-md border px-3"
                            placeholder="промокод"
                            :disabled="loadingCoupon"
                        />
                    </div>

                    <Button
                        type="button"
                        class="h-10"
                        :disabled="loadingCoupon || !couponCode"
                        @click="applyCoupon"
                    >
                        {{ loadingCoupon ? 'Проверяем…' : 'Применить' }}
                    </Button>
                </div>

                <InputError class="text-xs" :message="form.errors.coupon_code" />
            </section>

            <Button type="button" class="mx-auto w-fit" @click="submitWithPayment">
                Забронировать и оплатить ({{ finalPrice }} руб.)
            </Button>
            <Button
                type="button"
                variant="outline"
                class="mx-auto w-fit"
                @click="submitWithoutPayment"
                v-if="!couponCode"
            >
                Забронировать (оплата в студии)
            </Button>
        </template>
    </form>
</template>
