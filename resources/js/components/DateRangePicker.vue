<script setup lang="ts">
import { ref, watch } from 'vue';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/shadecn/popover';
import { Button } from '@/components/shadecn/button';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { RangeCalendar } from '@/components/shadecn/range-calendar';
import { DateRange } from 'reka-ui';
import { Matcher, toDate } from 'reka-ui/date';
import { DateFormatter } from '@internationalized/date';

const props = withDefaults(
    defineProps<{
        placeholderText?: string;
        isDateDisabled?: Matcher;
        class?: string;
    }>(),
    { placeholderText: 'Даты аренды', isDateDisabled: undefined },
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
