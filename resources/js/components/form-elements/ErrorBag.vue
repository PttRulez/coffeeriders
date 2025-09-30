<script setup lang="ts">
import InputError from '@/components/form-elements/InputError.vue';

interface Props {
    errors: (string | undefined)[];
}
const { errors } = defineProps<Props>();
</script>

<template>
    <InputError
        v-for="errorMessage in errors"
        :message="errorMessage"
        :key="errorMessage"
        v-bind="$attrs"
    />
</template>