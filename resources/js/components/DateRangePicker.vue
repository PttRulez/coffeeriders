<script setup lang="ts">
import { ref, watch } from 'vue';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/shadecn/popover';
import { Button } from '@/components/shadecn/button';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { RangeCalendar } from '@/components/shadecn/range-calendar';
import { DateRange, DateValue } from 'reka-ui';
import { Matcher, toDate } from 'reka-ui/date';
import { DateFormatter, today } from '@internationalized/date';

const props = withDefaults(
    defineProps<{
        placeholderText?: string;
        isDateDisabled?: Matcher;
         bookedDates?: string[];
        class?: string;
    }>(),
    { placeholderText: 'Даты аренды', bookedDates: () => [] },
);

const model = defineModel<DateRange>({
    default: { start: undefined, end: undefined },
});
const open = ref(false);
const placeholder = ref<any>();
const df = new DateFormatter('ru-RU', { dateStyle: 'short' });

watch(
    () => model.value,
    (r) => {
        if (r?.start && r?.end) open.value = false;
    },
    { deep: true },
);

const isDateDisabled = (day: DateValue): boolean => {
  const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  const todayDate = today(timeZone);
  const iso = day.toString();

  // прошлое всегда блокируем
  if (day.compare(todayDate) < 0) {
    return true;
  }

  // если start ещё не выбран → выбираем start
  if (!placeholder.value?.start) {
    return props.bookedDates.includes(iso);
  }

  // если start уже выбран → выбираем end
  if (props.bookedDates.includes(iso)) {
    // можно end = start (однодневная аренда)
    return iso !== placeholder.value.start.toString();
  }

  return false;
}
</script>

<template>
    <Popover :open="open" @update:open="(val) => (open = val)">
        <PopoverTrigger as-child>
            <Button
                type="button"
                variant="outline"
                :class="
                    cn(
                        'w-full justify-start p-6 text-xl text-left font-normal',
                        !model?.start && 'text-muted-foreground',
                        props.class
                    )
                "
            >
                <div class="md:text-sm">
                    <template v-if="model?.start && model?.end">
                        {{ df.format(toDate(model.start)) }} — {{ df.format(toDate(model.end)) }}
                    </template>
                    <template v-else-if="model?.start">
                        {{ df.format(toDate(model.start)) }} — …
                    </template>
                    <template v-else>
                        {{ props.placeholderText }}
                    </template>
                </div>
                <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
            </Button>
        </PopoverTrigger>

        <PopoverContent class="w-auto p-0" align="start">
            <RangeCalendar
                v-model="model"
                v-model:placeholder="placeholder"
                :is-date-disabled="isDateDisabled"
                :week-starts-on="1"
                initial-focus
                :hideTimeZone="false"
            />
        </PopoverContent>
    </Popover>
</template>

<style scoped></style>
