<script lang="ts" setup="">
import { Button } from '@/components/shadecn/button';

type Props = {
    href: string;
    text?: string;
};

const props = withDefaults(defineProps<Props>(), {
    text: 'Подробнее',
});

</script>

<template>
    <Button variant="default" class="p-6 text-lg md:p-8 md:text-2xl" as-child>
        <Link :href="props.href">{{ props.text }}</Link>
    </Button>
</template>
