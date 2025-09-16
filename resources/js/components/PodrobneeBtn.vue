<script lang="ts" setup="">
import { Button } from '@/components/ui/button';

type Props = {
    href: string;
}

const props = defineProps<Props>();
</script>

<template>
    <Button variant="default" class="p-6 text-lg md:p-10 md:text-2xl" as-child>
        <Link :href="props.href">Подробнее</Link>
    </Button>
</template>
