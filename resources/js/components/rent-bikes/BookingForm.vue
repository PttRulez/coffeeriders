<script setup lang="ts">
import FormInput from '@/components/form-elements/FormInput.vue';
import FormTextArea from '@/components/form-elements/FormTextArea.vue';
import InputError from '@/components/form-elements/InputError.vue';
import PhoneInput from '@/components/form-elements/PhoneInput.vue';
import DateRangePicker from '@/components/shared/DateRangePicker.vue';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { today } from '@internationalized/date';
import { LoaderCircle } from 'lucide-vue-next';
import { DateRange, DateValue } from 'reka-ui';
import { computed, ref } from 'vue';

type Props = {
    mode?: 'user' | 'admin';
    bike_id: number;
    booked_dates: Array<string>;
    predoplata: number;
};
const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'success'): void;
}>();
const mode = computed(() => props.mode ?? 'user');
const isAdminMode = computed(() => mode.value === 'admin');

const dateRange = ref<DateRange>({ start: undefined, end: undefined });

const form = useForm({
    bike_id: props.bike_id,
    comment: '',
    customer_name: '',
    telegram_username: '',
    phone: '',
    starts_at: '',
    ends_at: '',
});

function isDisabledDate(day: DateValue): boolean {
    const booked = props.booked_dates.includes(day.toString());
    const isInPast = day.compare(today('Europe/Moscow')) < 0;
    return booked || isInPast;
}

function submit(): void {
    form.starts_at = dateRange.value.start?.toString() ?? '';
    form.ends_at = dateRange.value.end?.toString() ?? '';

    if (isAdminMode.value) {
        form.post(route('adminka.rent-bikes.bookings.store'), {
            onSuccess: () => emit('success'),
        });
        return;
    }
    form.post(route('rent-bikes.booking.store', { bike: props.bike_id }), {
        onSuccess: () => emit('success'),
    });
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <FormInput
                field-name="customer_name"
                :placeholder="isAdminMode ? 'Имя клиента' : 'ваше имя'"
                v-model="form.customer_name"
                :error-message="form.errors.customer_name"
            />

            <div>
                <DateRangePicker
                    :is-date-disabled="isDisabledDate"
                    :booked-dates="props.booked_dates"
                    v-model="dateRange"
                    placeholderText="даты брони"
                />
                <InputError v-if="form.errors.starts_at" :message="form.errors.starts_at" />
                <InputError v-if="form.errors.ends_at" :message="form.errors.ends_at" />
            </div>

            <FormInput
                field-name="telegram_username"
                placeholder="telegram"
                v-model="form.telegram_username"
                :error-message="form.errors.telegram_username"
            />

            <PhoneInput
                field-name="phone"
                placeholder="телефон"
                v-model="form.phone"
                :error-message="form.errors.phone"
            />

            <FormTextArea
                wrapperClass="md:col-span-2"
                field-name="comment"
                placeholder="комментарий"
                v-model="form.comment"
            />
        </div>

        <Button type="submit" class="mt-2 w-full" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            {{ isAdminMode ? 'Создать бронь без оплаты' : `Оплатить ${props.predoplata} руб.` }}
        </Button>
    </form>
</template>

<style scoped></style>
